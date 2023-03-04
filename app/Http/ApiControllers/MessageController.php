<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageStoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(MessageStoreRequest $request)
    {
        $chatIds = Auth::user()->chats->pluck('id')->toArray();

        if (!in_array($request->get('chat_id'), $chatIds)) {
            throw new \Exception('Forbidden', 403);
        }

        try {
            $message = Message::create([
                'user_id' => Auth::id(),
                'chat_id' => $request->get('chat_id'),
                'body' => $request->get('body')
            ]);

            return response()->success(new MessageResource($message), 201);
        } catch (\Throwable $e) {
            return response()->error();
        }
    }

    public function destroy($id)
    {
        $message = Message::find($id);

        if ($message->user_id !== Auth::id()) {
            throw new \Exception('Forbidden', 403);
        }

        try {
            $message->delete();

            return response()->success();
        } catch (\Throwable $e) {
            return response()->error();
        }
    }
}
