<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShikiIdRequest extends FormRequest
{
    public function rules()
    {
        return [
            'imdb_id' => 'required|string'
        ];
    }
}
