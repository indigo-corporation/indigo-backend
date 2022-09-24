<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnlikeRequest extends FormRequest
{

    public function rules()
    {
        return [
            'comment_id' => 'required|int|exists:comments,id'
        ];
    }
}
