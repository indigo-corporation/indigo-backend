<?php

namespace App\Http\Requests;

use App\Models\Film\Film;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'category' => [
                'string',
                Rule::in(Film::CATEGORIES)
            ]
        ];
    }
}
