<?php

namespace App\Observers;

use App\Models\Film\Film;
use App\Services\ElasticService;

class FilmObserver
{
    /**
     * Handle the Film "created" event.
     */
    public function created(Film $film): void
    {
        if ((bool)env('ES_ON')) {
            (new ElasticService())->getClient()->index(
                [
                    'body' => [
                        'original_title' => $film->original_title,
                        'translations' => [
                            'title' => $film->title
                        ]
                    ],
                    'index' => 'films',
                    'type' => $film->category,
                    'id' => $film->id
                ]
            );
        }
    }

    /**
     * Handle the Film "updated" event.
     */
    public function updated(Film $film): void
    {
        //
    }

    /**
     * Handle the Film "deleted" event.
     */
    public function deleted(Film $film): void
    {
        if ((bool)env('ES_ON')) {
            (new ElasticService())->getClient()->delete([
                'index' => 'films',
                'type' => $film->category,
                'id' => $film->id
            ]);
        }
    }
}
