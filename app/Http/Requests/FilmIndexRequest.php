<?php

namespace App\Http\Requests;

use App\Models\Film\Film;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilmIndexRequest extends FormRequest
{
    public function rules()
    {
        return [
            'category' => [
                'string',
                Rule::in(Film::CATEGORIES)
            ],
            'sort_field' => [
                'string',
                Rule::in(Film::SORT_FIELDS)
            ],
            'sort_direction' => [
                'string',
                Rule::in(Film::SORT_DIRECTIONS)
            ],
            'genre_id' => [
                'int'
            ],
            'year' => [
                'int'
            ],
            'country_id' => [
                'int'
            ]
        ];
    }
}
