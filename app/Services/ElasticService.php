<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticService
{
    private Client $client;

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function __construct()
    {
        $clientBuilder = ClientBuilder::create()
            ->setHosts([env('ES_HOST')]);

        if (app()->isProduction()) {
            $clientBuilder = $clientBuilder
                ->setSSLVerification(false)
//                ->setCABundle(env('ES_CERT'))
                ->setBasicAuthentication(env('ES_USER'), env('ES_PASS'));
        }

        $this->client = $clientBuilder->build();
    }
}
