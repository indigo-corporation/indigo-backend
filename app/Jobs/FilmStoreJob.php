<?php

namespace App\Jobs;

use App\Models\Film\Film;
use App\Models\Genre\Genre;
use App\Services\GetFromUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class FilmStoreJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $imdbId;
    private GetFromUrlService $getService;

    public function __construct(string $imdbId)
    {
        $this->imdbId = $imdbId;
        $this->getService = new GetFromUrlService();
    }

    public function handle()
    {
        $imdbIdExists = Film::where('imdb_id', $this->imdbId)->exists();
        if ($imdbIdExists) {
            return;
        }

        $videocdnData = $this->getService->getVideocdnFilm($this->imdbId, true);
        if (!$videocdnData) {
            return;
        }

        $imdbData = $this->getService->getImdb($this->imdbId, true);
        if (!$imdbData) {
            return;
        }

        dump($this->imdbId);

        $rating = $imdbData->rating() !== '' ? $imdbData->rating() : null;
        $posterUrl = $imdbData->photo(false) ?? null;
        $runtime = $imdbData->runtime() ?? null;
        $overview = $imdbData->plotoutline();
        $year = $imdbData->year();

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
            'is_serial' => false,
            'poster' => $posterUrl,
            'runtime' => $runtime,
            'release_date' => $videocdnData->released,
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

        dump('stored');
    }
}
