<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserPictureStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'picture' => 'required|image'
        ];
    }
}
