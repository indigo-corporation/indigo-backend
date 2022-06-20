<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'original_title' => $this->original_title,
            'original_language' => $this->original_language,
            'poster_url' => $this->poster_url,
            'runtime' => $this->runtime,
            'release_date' => $this->release_date,
            'year' => $this->year,
            'imdb_id' => $this->imdb_id,
            'imdb_rating' => $this->imdb_rating,
            'title' => $this->title,
            'overview' => $this->overview,
            'genres' => GenreResource::collection($this->genres),
            'countries' => CountryResource::collection($this->countries)
        ];
    }
}
