<?php

namespace App\Jobs;

use App\Models\Film\Film;
use App\Models\Genre\Genre;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnimeStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $shikiId;
    private $imdbId;

    public function __construct($shikiId, $imdbId)
    {
        $this->shikiId = $shikiId;
        $this->imdbId = $imdbId;
    }

    public function handle()
    {
        $link = 'https://shikimori.one/api/animes/' . $this->shikiId;
        $shikiData = json_decode(file_get_contents($link));

        $is_serial = $shikiData->episodes !== 1;
        $poster_url = $shikiData->image->original
            ? 'https://shikimori.one' . $shikiData->image->original
            : null;

        $imdbRating = null;
        if($this->imdbId) {
            try {
                $imdbData = new \Imdb\Title($this->imdbId);
                $poster_url = $imdbData->photo(false) ?? $poster_url;
                $imdbRating = $imdbData->rating() ?? null;
            } catch (\Throwable $e) {
                if (!Str::contains($e->getMessage(), 'Status code [404]')) {
                    throw $e;
                }
            }
        }

        if (empty($imdbRating)) $imdbRating = null;

        $film = Film::create([
            'original_title' => $shikiData->name,
            'original_language' => 'ja',
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
            'ru' => [
                'title' => $shikiData->russian,
                'overview' => $shikiData->description,
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
        $film->savePosterThumbs($film->poster);
    }
}
