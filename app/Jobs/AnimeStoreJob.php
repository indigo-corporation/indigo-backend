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
        $is_serial = $this->film->episodes !== 1;
        $poster_url = $this->film->image->original
            ? 'https://shikimori.one' . $this->film->image->original
            : null;

        $film = Film::create([
            'original_title' => $this->film->name,
            'original_language' => 'ja',
            'poster_url' => $poster_url,
            'release_date' => $this->film->aired_on,
            'year' => (new Carbon($this->film->aired_on))?->year,
            'runtime' => $this->film->duration,
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
