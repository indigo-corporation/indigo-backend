<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SearchRequest;
use App\Http\Requests\Api\UserPictureStoreRequest;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Resources\Api\MyUserResource;
use App\Http\Resources\Api\PaginatedCollection;
use App\Http\Resources\Api\UserResource;
use App\Http\Resources\Api\UserShortResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use function response;

class UserController extends Controller
{
    public function changePass(Request $request)
    {
        $attr = $request->validate([
            'password' => 'required|string|min:6'
        ]);

        $user = Auth::user();
        $user->password = Hash::make($attr['password']);
        $user->save();

        return response()->success();
    }

    public function changeInfo(UserStoreRequest $request)
    {
        $user = Auth::user();

        $user->update($request->all());

        return response()->success(new MyUserResource($user));
    }

    public function changePicture(UserPictureStoreRequest $request)
    {
        $file = $request->file('picture');
        $user = Auth::user();

        $user->savePosterThumbs($file);

        return response()->success(new MyUserResource($user));
    }

    public function get($user_id)
    {
        $user = User::findOrFail($user_id);

        return response()->success(new UserResource($user));
    }

    public function search(SearchRequest $request)
    {
        $users = User::where('name', 'ilike', $request->find . '%')
            ->where('id', '<>', Auth::id());

        return response()->success_paginated(
            new PaginatedCollection($users->paginate(20), UserShortResource::class)
        );
    }
}
