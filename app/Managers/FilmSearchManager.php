<?php

namespace App\Managers;

use App\Models\Film\Film;
use App\Services\ElasticService;
use Illuminate\Database\Eloquent\Builder;

class FilmSearchManager
{
    private bool $isElastic;

    private ?string $category = null;

    private ?int $genreId = null;

    private ?int $year = null;

    private ?int $countryId = null;

    public function __construct(
        private string $find
    ) {
        $this->isElastic = (bool)env('ES_ON', false);
    }

    public function getQuery(): Builder
    {
        $query = Film::getListQuery(
            $this->category,
            $this->genreId,
            $this->year,
            $this->countryId
        );

        if ($this->isElastic) {
            try {
                return $this->getElasticQuery($query);
            } catch (\Throwable $e) {
                \Log::error('getElasticQuery', [
                    'message' => $e->getMessage()
                ]);
            }
        }

        return $this->getDbSearchQuery($query);
    }

    private function getDbSearchQuery(Builder $query): Builder
    {
        return $query->whereTranslationIlike('title', '%' . $this->find . '%');
    }

    private function getElasticQuery(Builder $query): Builder
    {
        $client = (new ElasticService())->getClient();

        $response = $client->search([
            'index' => 'films',
            'type' => 'anime',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $this->find,
                        'fields' => ['original_title', 'translations.title'],
                        'fuzziness' => 'auto:4,6'
                    ]
                ]
            ]
        ]);

        $filmIds = collect($response['hits']['hits'])->pluck(['_id'])->toArray();

        return $query
            ->whereIn('id', $filmIds)
            ->orderByRaw("array_position('{" . implode(',', $filmIds) . "}'::int[], id)");
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function setGenreId(?int $genreId): void
    {
        $this->genreId = $genreId;
    }

    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    public function setCountryId(?int $countryId): void
    {
        $this->countryId = $countryId;
    }
}
