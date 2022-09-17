<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FavoriteFilmsRequest extends FormRequest
{

    public function rules()
    {
        return [
            'film_id' => 'required|integer'
        ];
    }
}
