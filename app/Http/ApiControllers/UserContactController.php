<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\PaginatedCollection;
use App\Http\Resources\UserShortResource;
use App\Models\UserContact;
use Illuminate\Support\Facades\Auth;

class UserContactController extends Controller
{

    public function all()
    {
        $users = Auth::user()->contact_users();

        return response()->success_paginated(
            new PaginatedCollection($users->paginate(20), UserShortResource::class)
        );
    }

    public function allIDs()
    {
        $IDs = UserContact::where('user_id', Auth::id())->pluck('contact_id')->toArray();

        return response()->success($IDs);
    }

    public function remove(UserRequest $request)
    {
        $user = Auth::user();

        UserContact::query()
            ->where('user_id', $user->id)
            ->where('contact_id', $request->user_id)
            ->delete();
        UserContact::query()
            ->where('user_id', $request->user_id)
            ->where('contact_id', $user->id)
            ->delete();

        return response()->success(null, 204);
    }

    public function search(SearchRequest $request)
    {
        $users = Auth::user()->contact_users()->where('name', 'ilike', $request->find . '%');

        return response()->success_paginated(
            new PaginatedCollection($users->paginate(20), UserShortResource::class)
        );
    }
}
