<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FilmIdRequest extends FormRequest
{
    public function rules()
    {
        return [
            'film_id' => 'required|int'
        ];
    }
}
