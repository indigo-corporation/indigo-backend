<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilmStarStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'film_id' => 'required|integer|exists:films,id',
            'count' => 'required|integer|min:1|max:5'
        ];
    }
}
