<?php

namespace App\Jobs;

use App\Models\Film\Film;
use App\Services\GetFromUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class UpdateDescriptionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Film $film;

    private GetFromUrlService $getService;

    public function __construct(Film $film)
    {
        $this->film = $film;
        $this->getService = new GetFromUrlService();
    }

    public function handle()
    {
        try {
            $tmdbData = $this->getService->getTmdbFilmItemByImdbId($this->film->imdb_id);

            if (!isset($tmdbData->movie_results) || !$tmdbData->movie_results) {
                return;
            }

            $tmdbFilm = ($tmdbData->movie_results)[0];

            if (!$tmdbFilm->overview) return;

            dump($this->film->imdb_id);

            $this->film->update([
                'ru' => [
                    'overview' => $tmdbFilm->overview
                ]
            ]);

            sleep(2);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
