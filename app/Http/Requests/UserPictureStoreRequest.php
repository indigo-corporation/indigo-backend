<?php

namespace App\Http\Requests;

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
