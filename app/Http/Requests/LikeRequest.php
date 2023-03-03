<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LikeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'comment_id' => 'required|int|exists:comments,id',
            'is_like' => 'required|bool'
        ];
    }
}
