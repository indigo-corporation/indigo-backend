<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'original_title' => $this->original_title,
            'poster' => $this->poster_medium,
            'poster_small' => $this->poster_small,
            'poster_medium' => $this->poster_medium,
            'runtime' => $this->runtime,
            'release_date' => $this->release_date,
            'year' => $this->year,
            'imdb_id' => $this->imdb_id,
            'imdb_rating' => $this->imdb_rating,
            'shiki_id' => $this->shiki_id,
            'shiki_rating' => $this->shiki_rating,
            'is_anime' => $this->is_anime,
            'is_serial' => $this->is_serial,
            'title' => $this->title,
            'overview' => $this->overview,
            'genres' => GenreResource::collection($this->genres),
            'countries' => CountryResource::collection($this->countries),
            'stars' => $this->stars()->avg('count'),
            'slug' => $this->slug,
            'category' => $this->category
        ];
    }
}
