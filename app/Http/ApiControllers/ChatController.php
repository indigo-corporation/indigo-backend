<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\ChatResource;
use App\Http\Resources\ChatShortResource;
use App\Http\Resources\FilmResource;
use App\Http\Resources\PaginatedCollection;
use App\Models\Chat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        return response()->success_paginated(
            new PaginatedCollection(Auth::user()->chats()->paginate(10), ChatShortResource::class)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $chatIds = Auth::user()->chats->pluck('id')->toArray();

        if (!in_array($id, $chatIds)) {
            throw new \Exception('Forbidden', 403);
        }

        return response()->success(new ChatResource(Chat::find($id)));
    }

    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $chatIds = Auth::user()->chats->pluck('id')->toArray();

        if (!in_array($id, $chatIds)) {
            throw new \Exception('Forbidden', 403);
        }

        try {
            Chat::find($id)->delete();

            return response()->success();
        } catch (\Throwable $e) {
            return response()->error();
        }
    }

    public function getByUser(UserRequest $request)
    {
        $user_id = $request->get('user_id');

        $chat = Auth::user()->chats()->whereHas('users', function (Builder $query) use($user_id) {
            $query->where('users.id', $user_id);
        })->first();

        if (!$chat) {
            $chat = Chat::create();
            $chat->users()->attach([Auth::id(), $user_id]);
        }

        return response()->success(new ChatResource($chat));
    }
}
