<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\PaginatedCollection;
use App\Http\Resources\UserShortResource;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Support\Facades\Auth;

class UserContactController extends Controller
{

    public function all()
    {
        return response()->success_paginated(
            new PaginatedCollection(Auth::user()->contact_users()->paginate(20), UserShortResource::class)
        );
    }

    public function allIDs()
    {
        $IDs = Auth::user()->contacts()->pluck('contact_id')->toArray();

        return response()->success($IDs);
    }

    public function add(UserRequest $request)
    {
        $user = Auth::user();

        $exists = UserContact::query()
            ->where('user_id', $user->id)
            ->where('contact_id', $request->user_id)
            ->exists();

        if (!$exists) {
            $user->contacts()->create([
                'user_id' => $user->id,
                'contact_id' => $request->user_id,
            ]);
        }

        return response()->success(new UserShortResource(User::find($request->user_id)), 201);
    }

    public function remove(UserRequest $request)
    {
        $user = Auth::user();

        $exists = UserContact::query()
            ->where('user_id', $user->id)
            ->where('contact_id', $request->user_id)
            ->exists();

        if ($exists) {
            $user->contacts()->where([
                'user_id' => $user->id,
                'contact_id' => $request->user_id,
            ])->delete();
        }

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
