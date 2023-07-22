<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GenresGetRequest extends FormRequest
{
    public function rules()
    {
        return [
            'is_anime' => 'boolean'
        ];
    }
}
