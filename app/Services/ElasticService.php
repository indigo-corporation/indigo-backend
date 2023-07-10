<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticService
{
    public static function getClient(): Client
    {
        $clientBuilder = ClientBuilder::create()
            ->setHosts([env('ES_HOST')]);

        if (app()->isProduction()) {
            $clientBuilder = $clientBuilder
                ->setBasicAuthentication(env('ES_USER'), env('ES_PASS'))
                ->setCABundle(env('ES_CERT'));
        }

        return $clientBuilder->build();
    }
}
