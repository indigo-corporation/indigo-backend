<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MyUserResource extends JsonResource
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
            'name' => $this->name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'birth_date' => $this->birth_date,
            'about' => $this->about,
            //            'city' => new CityResource($this->city),
            //            'country' => new CountryResource($this->country),
            'poster_small' => $this->poster_small,
            'poster_medium' => $this->poster_medium,
            'poster_large' => $this->poster_large,
            'favorite_film_ids' => $this->favorite_film_ids
        ];
    }
}
