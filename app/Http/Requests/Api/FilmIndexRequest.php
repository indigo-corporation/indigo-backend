<?php

namespace App\Http\Requests\Api;

use App\Models\Film\Film;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilmIndexRequest extends FormRequest
{
    public function rules()
    {
        return [
            'category' => [
                'nullable',
                'string',
                Rule::in(Film::CATEGORIES)
            ],
            'sort_field' => [
                'nullable',
                'string',
                Rule::in(Film::SORT_FIELDS)
            ],
            'sort_direction' => [
                'nullable',
                'string',
                Rule::in(Film::SORT_DIRECTIONS)
            ],
            'genre_id' => [
                'nullable',
                'int'
            ],
            'year' => [
                'nullable',
                'int'
            ],
            'country_id' => [
                'nullable',
                'int'
            ]
        ];
    }
}
