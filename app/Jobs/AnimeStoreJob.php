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

class AnimeStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $film;

    public function __construct($film)
    {
        $this->film = $film;
    }

    public function handle()
    {
        $shikiIdExists = Film::where('imdb_id', $this->film->id)->exists();
        if($shikiIdExists) return;

        $is_serial = $this->film->episodes !== 1;
        $poster_url = $this->film->image->original
            ? 'https://shikimori.one' . $this->film->image->original
            : null;

        $kodikUrl = 'https://kodikapi.com/search?token=c93d194dd1a2f6cc95b3095a9940dfb2&shikimori_id=' . $this->film->id;
        $kodikData = json_decode(file_get_contents($kodikUrl))->results;

        $imdbId = null;
        if($kodikData) {
            $imdbId = (($kodikData[0])->imdb_id);
        }

        if($imdbId) {
            $imdbData = new \Imdb\Title($imdbId);
            $poster_url = $imdbData->photo(false) ?? $poster_url;

            $imdbIdExists = Film::where('imdb_id', $imdbId)->exists();
            if($imdbIdExists) return;
        }

        $film = Film::create([
            'original_title' => $this->film->name,
            'original_language' => 'ja',
            'poster_url' => $poster_url,
            'release_date' => $this->film->aired_on,
            'year' => (new Carbon($this->film->aired_on))?->year,
            'runtime' => $this->film->duration,
            'imdb_id' => $imdbId,
            'imdb_rating' => $imdbData->rating() !== '' ? $imdbData->rating() : null,
            'shiki_id' => $this->film->id,
            'shiki_rating' => (float)$this->film->score,
            'is_anime' => true,
            'is_serial' => $is_serial,
            'ru' => [
                'title' => $this->film->russian,
                'overview' => $this->film->description,
            ]
        ]);

        //get genres
        $genres = [];
        foreach ($this->film->genres as $genre) {
            $genreModel = Genre::where('name', strtolower($genre->name))
                ->where('is_anime', true)
                ->first();
            if ($genreModel) {
                $genres[] = $genreModel->id;
            }
        }

        $film->genres()->attach($genres);
    }
}
