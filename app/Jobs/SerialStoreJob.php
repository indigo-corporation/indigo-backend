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

    private $film;

    public function __construct($film)
    {
        $this->film = $film;
    }

    public function handle()
    {
        $imdbIdExists = Film::where('imdb_id', $this->film->imdb_id)->exists();
        if($imdbIdExists) return;

        try {
            $imdbData = new \Imdb\Title($this->film->imdb_id);
            $rating = $imdbData->rating() !== '' ? $imdbData->rating() : null;
            $posterUrl = $imdbData->photo(false) ?? null;
            $runtime = $imdbData->runtime() ?? null;
            $overview = $imdbData->plotoutline();
        } catch (\Throwable $e) {
            if (Str::contains($e->getMessage(), 'Status code [404]')) {
                return;
            } else {
                throw $e;
            }
        }

        dump($this->film->imdb_id);
        sleep(10);
        // TODO: actors, directors
//         dd(
//             $imdbData->actor_stars(),
//             $imdbData->director(), // режиссеры
//         );

        $year = (new Carbon($this->film->start_date))->year ?? null;

        $film = Film::create([
            'original_title' => $this->film->orig_title,
            'imdb_id' => $this->film->imdb_id,
            'imdb_rating' => $rating,
            'is_anime' => false,
            'is_serial' => true,
            'poster_url' => $posterUrl,
            'runtime' => $runtime,
            'release_date' => $this->film->start_date,
            'year' => $year,
            'ru' => [
                'title' => $this->film->ru_title,
                'overview' => $overview
            ]
        ]);

        $genres = [];
        foreach ($imdbData->genres() as $genre) {
            $genreModel = Genre::where('name', lcfirst($genre))
                ->where('is_anime', false)
                ->first();
            if ($genreModel) {
                $genres[] = $genreModel->id;
            }
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
    }
}
