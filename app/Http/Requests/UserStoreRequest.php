<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
            ],
            'user_name' => [
                'required',
                'string',
                'min:2',
                'max:30',
                Rule::unique('users')->ignore(Auth::id())
            ],
            'birth_date' => 'date',
            'about' => 'string',
            'city_id' => 'integer'
        ];
    }
}
