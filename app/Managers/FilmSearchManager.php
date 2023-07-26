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
    )
    {
        $this->isElastic = (bool)env('ES_ON', false);
    }

    public function getQuery()
    {
        $query = Film::getListQuery(
            $this->category,
            $this->genreId,
            $this->year,
            $this->countryId
        );

        $query = $this->isElastic
            ? $this->getElasticQuery($query)
            : $this->getDbSearchQuery($query);

        return $query;
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

    /**
     * @param string|null $category
     */
    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    /**
     * @param int|null $genreId
     */
    public function setGenreId(?int $genreId): void
    {
        $this->genreId = $genreId;
    }

    /**
     * @param int|null $year
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    /**
     * @param int|null $countryId
     */
    public function setCountryId(?int $countryId): void
    {
        $this->countryId = $countryId;
    }
}
