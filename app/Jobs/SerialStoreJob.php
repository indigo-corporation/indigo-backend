<?php

namespace App\Jobs;

use App\Models\Film\Film;
use App\Models\Genre\Genre;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SerialStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $imdbId;

    public function __construct($imdbId)
    {
        $this->imdbId = $imdbId;
    }

    public function handle()
    {
        $link = env('VIDEOCDN_API') . 'tv-series'
            . '?api_token=' . env('VIDEOCDN_TOKEN')
            . '&field=imdb_id&query=' . $this->imdbId;

        $videocdnResponse = json_decode(file_get_contents($link));
        $videocdnData = ($videocdnResponse->data)[0] ?? null;

        if (!$videocdnData) return;

        try {
            $imdbData = new \Imdb\Title($videocdnData->imdb_id);
            $rating = $imdbData->rating() !== '' ? $imdbData->rating() : null;
            $posterUrl = $imdbData->photo(false) ?? null;
            $runtime = $imdbData->runtime() ?? null;
            $overview = $imdbData->plotoutline();
            $year = $imdbData->year();
        } catch (\Throwable $e) {
            if (Str::contains($e->getMessage(), 'Status code [404]')) {
                return;
            } else {
                throw $e;
            }
        }
        dump($this->imdbId);

        // TODO: actors, directors
//         dd(
//             $imdbData->actor_stars(),
//             $imdbData->director(), // режиссеры
//         );

        $film = Film::create([
            'original_title' => $videocdnData->orig_title,
            'imdb_id' => $videocdnData->imdb_id,
            'imdb_rating' => $rating,
            'is_anime' => false,
            'is_serial' => true,
            'poster' => $posterUrl,
            'runtime' => $runtime,
            'release_date' => $videocdnData->start_date,
            'year' => $year,
            'ru' => [
                'title' => $videocdnData->ru_title,
                'overview' => $overview
            ]
        ]);

        $genres = [];
        foreach ($imdbData->genres() as $genre) {
            $genreModel = Genre::firstOrCreate([
                'name' => lcfirst($genre),
                'is_anime' => false
            ]);

            $genres[] = $genreModel->id;
        }

        //get countries
        $countries = [];
        foreach ($imdbData->country() as $country) {
            $countryModel = DB::table('countries')
                ->where('name', $country)->first();
            if ($countryModel) {
                $countries[] = $countryModel->id;
            }
        }

        $film->genres()->attach($genres);
        $film->countries()->attach($countries);

        $film->updateCategory();
        $film->savePosterThumbs($film->poster);
    }
}
