<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name' => 'required|string',
            'birth_date' => 'date',
            'about' => 'string',
            'city_id' => 'integer'
        ];
    }
}
