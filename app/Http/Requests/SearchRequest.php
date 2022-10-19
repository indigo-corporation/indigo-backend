<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{

    public function rules()
    {
        return [
            'find' => 'required|string|min:2'
        ];
    }
}
