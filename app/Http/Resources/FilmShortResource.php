<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilmShortResource extends JsonResource
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
            'poster' => $this->poster_small,
            'poster_small' => $this->poster_small,
            'year' => $this->year,
            'imdb_rating' => $this->imdb_rating,
            'shiki_rating' => $this->shiki_rating,
            'title' => $this->title,
            'overview' => $this->overview,
            'genres' => GenreResource::collection($this->genres),
            'slug' => $this->slug,
            'category' => $this->category
        ];
    }
}
