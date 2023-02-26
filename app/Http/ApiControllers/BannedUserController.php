<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\PaginatedCollection;
use App\Http\Resources\UserShortResource;
use App\Models\BannedUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BannedUserController extends Controller
{
    public function all()
    {
        $users = Auth::user()->banned_users();

        return response()->success_paginated(
            new PaginatedCollection($users->paginate(20), UserShortResource::class)
        );
    }

    public function allIDs()
    {
        $IDs = BannedUser::where('user_id', Auth::id())->pluck('banned_user_id')->toArray();

        return response()->success($IDs);
    }

    public function add(UserRequest $request)
    {
        $user = Auth::user();

        if ($request->user_id == $user->id) {
            return response()->error();
        }

        BannedUser::firstOrCreate([
            'user_id' => $user->id,
            'banned_user_id' => $request->user_id,
        ]);

        return response()->success(User::find($request->user_id), 201);
    }

    public function remove(UserRequest $request)
    {
        $user = Auth::user();

        BannedUser::query()
            ->where('user_id', $user->id)
            ->where('banned_user_id', $request->user_id)
            ->delete();

        return response()->success(null, 204);
    }

    public function search(SearchRequest $request)
    {
        $users = Auth::user()->banned_users()->where('name', 'ilike', $request->find . '%');

        return response()->success_paginated(
            new PaginatedCollection($users->paginate(20), UserShortResource::class)
        );
    }
}
