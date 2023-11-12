<?php

namespace App\Jobs;

use App\Models\Film\Film;
use App\Models\Genre\Genre;
use App\Services\GetFromUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SerialStoreJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $imdbId;

    private bool $force;

    private GetFromUrlService $getService;

    public function __construct(string $imdbId, $force = false)
    {
        $this->imdbId = $imdbId;
        $this->force = $force;
        $this->getService = new GetFromUrlService();
    }

    public function handle()
    {
        if (strlen($this->imdbId) > 10) {
            return;
        }

        $imdbIdExists = Film::where('imdb_id', $this->imdbId)->exists();
        if ($imdbIdExists) {
            return;
        }

        $videocdnData = $this->getService->getVideocdnSerial($this->imdbId, true);
        if (!$videocdnData) {
            return;
        }

        $imdbData = $this->getService->getImdb($this->imdbId, true);
        if (!$imdbData) {
            return;
        }

        try {
            $rating = $imdbData->rating() ? (float)$imdbData->rating() : null;
            $votes = $imdbData->votes();

            $countries = [];
            foreach ($imdbData->country() as $country) {
                $countryModel = DB::table('countries')
                    ->where('name', $country)->first();
                if ($countryModel) {
                    $countries[] = $countryModel->id;

                    if ($countryModel->iso2 === 'RU') {
                        $this->force = true;
                    }
                }
            }

            if (!$this->force) {
                if ($rating && $rating < 5) {
                    return;
                }
                if ($votes < 500) {
                    return;
                }
            }

            $posterUrl = $imdbData->photo(false) ?: null;
            $runtime = $imdbData->runtime() ?: null;
            $overview = $imdbData->plotoutline() ?: null;
            $year = $imdbData->year() ?: null;
        } catch (\Throwable $e) {
            if (Str::contains($e->getMessage(), 'Status code [404]')) {
                dump('not found');

                return;
            }

            throw $e;
        }

        // TODO: actors, directors
//         dd(
//             $imdbData->actor_stars(),
//             $imdbData->director(), // режиссеры
//         );
        dump($this->imdbId);

        $film = Film::create([
            'original_title' => $videocdnData->orig_title,
            'imdb_id' => $this->imdbId,
            'imdb_rating' => $rating,
            'imdb_votes' => $votes,
            'is_anime' => false,
            'is_serial' => true,
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
            $genre = strtolower($genre);

            if ($genre === 'animation') {
                $film->is_cartoon = true;
            } else {
                $genreModel = Genre::firstOrCreate([
                    'name' => $genre,
                    'is_anime' => false
                ]);

                $genres[] = $genreModel->id;
            }
        }

        $film->genres()->attach($genres);
        $film->countries()->attach($countries);

        $film->updateCategory();

        if ($film->poster) {
            StorePosterJob::dispatch($film);
        }

        UpdateDescriptionJob::dispatch($film);

        dump('stored');
    }
}
