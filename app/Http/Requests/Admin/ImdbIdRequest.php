<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImdbIdRequest extends FormRequest
{
    public function rules()
    {
        return [
            'shiki_id' => 'required|int'
        ];
    }
}
