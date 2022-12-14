<?php

namespace App\Http\ApiControllers;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\UserPictureStoreRequest;
use App\Http\Requests\UserRequest;
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

    /**
     * @OA\Post (
     *     path="/users/change-pass",
     *     operationId="change-pass",
     *     tags={"Users"},
     *     security={ {"sanctum": {} }},
     *     summary="Change pass",
     *     @OA\RequestBody(
     *         @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="password", type="string"),
     *                  required={"password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success response",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultSuccessResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
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

    /**
     * @OA\Post (
     *     path="/users/change-info",
     *     operationId="change-info",
     *     tags={"Users"},
     *     security={ {"sanctum": {} }},
     *     summary="Change info",
     *     @OA\RequestBody(
     *         @OA\MediaType(mediaType="multipart/form-data",
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success response",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultSuccessResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
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

        $file->move(public_path().'/images/user_posters/',$user->id.'.jpg');

        $user->poster_url = '/images/user_posters/'.$user->id.'.jpg';

        return response()->success($user->save());
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
