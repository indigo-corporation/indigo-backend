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

class UpdateImdbJob implements ShouldQueue
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
        $imdbData = $this->getService->getImdb($this->film->imdb_id, true);

        if (!$imdbData) {
            return;
        }

        dump($this->film->imdb_id);

        try {
            $rating = $imdbData->rating() ?: null;
            $votes = $imdbData->votes();
        } catch (\Throwable $e) {
            if (Str::contains($e->getMessage(), 'Status code [404]')) {
                dump('not found');

                return;
            }

            throw $e;
        }

        $this->film->imdb_rating = $rating;
        $this->film->imdb_votes = $votes;
        $this->film->save();
    }
}
