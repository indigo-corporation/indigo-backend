<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name' => 'string|min:2|max:50',
            'user_name' => 'string|min:2|unique:users,user_name',
            'birth_date' => 'date',
            'about' => 'string',
            'city_id' => 'integer'
        ];
    }
}
