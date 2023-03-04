<?php

namespace App\Http\ApiControllers;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\UserPictureStoreRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\PaginatedCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserShortResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use function response;

class UserController extends Controller
{
    public function sendResetPass(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->success($status === Password::RESET_LINK_SENT);
    }

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

        return response()->success(new UserResource($user));
    }

    public function changePicture(UserPictureStoreRequest $request)
    {
        $file = $request->file('picture');
        $user = Auth::user();

        $file->move(public_path() . '/images/user_posters/', $user->id . '.jpg');

        $user->poster_url = '/images/user_posters/' . $user->id . '.jpg';

        return response()->success(
            $user->save()->poster_url
        );
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
