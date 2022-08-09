<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name' => 'required',
            'birth_date' => 'string',
            'about' => 'string',
            'city_id' => 'integer'
        ];
    }
}
