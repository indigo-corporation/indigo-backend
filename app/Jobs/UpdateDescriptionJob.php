<?php

namespace App\Jobs;

use App\Models\Film\Film;
use App\Services\GetFromUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        } catch (\Throwable $e) {
            throw $e;
        }

        $resultField = $this->film->category === Film::CATEGORY_FILM
            ? 'movie_results'
            : 'tv_results';

        if (!isset($tmdbData->$resultField) || !$tmdbData->$resultField) {
            return;
        }

        $tmdbFilm = ($tmdbData->$resultField)[0];

        if (!$tmdbFilm->overview) {
            return;
        }

        dump($this->film->imdb_id);

        if ($this->film->translate('ru')) {
            $this->film->update([
                'ru' => [
                    'overview' => $tmdbFilm->overview
                ]
            ]);

            return;
        }

        $this->film->update([
            'ru' => [
                'title' => $tmdbFilm->title,
                'overview' => $tmdbFilm->overview
            ]
        ]);
    }
}
