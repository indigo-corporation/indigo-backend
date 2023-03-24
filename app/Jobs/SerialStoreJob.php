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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SerialStoreJob implements ShouldQueue
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

        $videocdnData = $this->getService->getVideocdnSerial($this->imdbId, true);
        if (!$videocdnData) {
            return;
        }

        $imdbData = $this->getService->getImdb($this->imdbId, true);
        if (!$imdbData) {
            return;
        }

        dump($this->imdbId);

        try {
            $rating = $imdbData->rating() ?: null;
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

        $film = Film::create([
            'original_title' => $videocdnData->orig_title,
            'imdb_id' => $videocdnData->imdb_id,
            'imdb_rating' => $rating,
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

        if ($film->poster) {
            try {
                $tempName = 'temp_' . $film->id;
                Storage::disk('public')->put(Film::THUMB_FOLDER . '/' . $tempName, file_get_contents($film->poster));

                $tempFile = storage_path('app/public') . '/' . Film::THUMB_FOLDER . '/' . $tempName;

                $film->savePosterThumbs($tempFile);

                Storage::disk('public')->delete(Film::THUMB_FOLDER . '/' . $tempName);
            } catch (\Throwable $e) {
                dump('Poster error');
                dump($e->getMessage());
            }
        }

        dump('stored');
    }
}
