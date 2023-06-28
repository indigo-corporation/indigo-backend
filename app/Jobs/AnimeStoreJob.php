<?php

namespace App\Jobs;

use App\Models\Film\Film;
use App\Models\Genre\Genre;
use App\Services\GetFromUrlService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AnimeStoreJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $shikiId;

    private string|null $imdbId = null;

    private GetFromUrlService $getService;

    public function __construct(int $shikiId)
    {
        $this->shikiId = $shikiId;
        $this->getService = new GetFromUrlService();
    }

    public function handle()
    {
        if (!$this->needToStore()) {
            return;
        }

        $shikiData = $this->getService->getShiki($this->shikiId, true);
        if (!$shikiData) {
            return;
        }

        $is_serial = $shikiData->episodes !== 1;
        $poster_url = $shikiData->image->original
            ? 'https://shikimori.one' . $shikiData->image->original
            : null;

        $imdbRating = null;
        if ($this->imdbId) {
            $imdbData = $this->getService->getImdb($this->imdbId, true);
            if ($imdbData) {
                $poster_url = $imdbData->photo(false) ?: $poster_url;
                $imdbRating = $imdbData->rating() ?: null;
            }
        }

        $re = '/(\[.*?\])/m';
        $description = preg_replace($re, '', $shikiData->description);

        dump($this->shikiId);

        $film = Film::create([
            'original_title' => $shikiData->name,
            'poster' => $poster_url,
            'release_date' => $shikiData->aired_on,
            'year' => (new Carbon($shikiData->aired_on))?->year,
            'runtime' => $shikiData->duration,
            'imdb_id' => $this->imdbId,
            'imdb_rating' => $imdbRating,
            'shiki_id' => $shikiData->id,
            'shiki_rating' => (float)$shikiData->score,
            'is_anime' => true,
            'is_serial' => $is_serial,
            'is_cartoon' => true,
            'ru' => [
                'title' => $shikiData->russian,
                'overview' => $description,
            ]
        ]);

        $genres = [];
        foreach ($shikiData->genres as $genre) {
            $genreModel = Genre::where('name', strtolower($genre->name))
                ->where('is_anime', true)
                ->first();

            if ($genreModel) {
                $genres[] = $genreModel->id;
            }
        }

        //get countries
        if (isset($imdbData)) {
            $countries = [];
            foreach ($imdbData->country() as $country) {
                $countryModel = DB::table('countries')
                    ->where('name', $country)->first();
                if ($countryModel) {
                    $countries[] = $countryModel->id;
                }
            }
            $film->countries()->attach($countries);
        }

        $film->genres()->attach($genres);

        $film->updateCategory();

        if ($film->poster) {
            try {
                $film->savePoster();
            } catch (\Throwable $e) {
                dump('Poster error');
                dump($e->getMessage());
            }
        }

        dump('stored');
    }

    private function needToStore(): bool
    {
        $shikiIdExists = Film::where('shiki_id', $this->shikiId)->exists();
        if ($shikiIdExists) {
            return false;
        }

        $kodikData = $this->getService->getKodik($this->shikiId, true);
        if (!$kodikData) {
            return false;
        }

        if (isset($kodikData->imdb_id)) {
            $this->imdbId = $kodikData->imdb_id;
        }

        return true;
    }
}
