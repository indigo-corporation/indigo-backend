<?php

namespace App\Http\Requests\Api;

use App\Models\Film\Film;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchRequest extends FormRequest
{
    public function rules()
    {
        return [
            'find' => 'required|string|min:2',
            'category' => [
                'nullable',
                'string',
                Rule::in(Film::CATEGORIES)
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
