<?php

namespace App\Console\Commands;

use App\Models\Film\Film;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdateElastic extends Command
{
    protected $signature = 'update-elastic';

    protected $description = 'update-elastic';

    private Client $client;

    public function __construct()
    {
        parent::__construct();

        $this->client = ClientBuilder::create()
            ->setHosts([env('ES_HOST')])
            ->build();
    }

    public function handle()
    {
        $left = Film::with('translations')->count();

        $chunkSize = 50;
        $i = 0;
        Film::with('translations')
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$i, $chunkSize, &$left) {
                foreach ($films as $film) {
                    $data = [
                        'body' => [
                            'original_title' => $film->original_title,
                            'translations' => [
                                'title' => $film->title
                            ]
                        ],
                        'index' => 'films',
                        'type' => $film->category,
                        'id' => $film->id
                    ];

                    try {
                        $this->client->index($data);
                    } catch (\Throwable $e) {
                        dd($e);
                    }
                }

                if (++$i * $chunkSize % 1000 === 0) {
                    $left -= 1000;

                    dump(
                        $left . ' left'
                    );
                }
            });
    }
}
