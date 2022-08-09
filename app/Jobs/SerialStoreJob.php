<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\Film\Film;
use App\Models\Genre\Genre;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $item = Film::create([
            'original_title' => $this->film->orig_title,
            'imdb_id' => $this->film->imdb_id,
            'imdb_rating' => null,
            'shiki_id' => null,
            'is_anime' => false,
            'is_serial' => true,
            'ru' => [
                'title' => $this->film->ru_title
            ]
        ]);
        $title = new \Imdb\Title($item->imdb_id);

        $item->poster_url = $title->photo();
        $item->poster_url = $title->photo();

        $rating = $title->rating();
        $plotOutline = $title->plotoutline();
        dd($rating,$plotOutline, $title->photo());
    }
}
