<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageStoreRequest extends FormRequest
{

    public function rules()
    {
        return [
            'chat_id' => 'required|integer|exists:chats,id',
            'body' => 'required|string|max:2000'
        ];
    }
}
