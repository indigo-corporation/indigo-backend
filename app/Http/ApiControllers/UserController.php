<?php

namespace App\Http\ApiControllers;

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

    public function storeUser(Request $request)
    {
        $user = Auth::user();

        foreach ($request->all() as $key => $input) {
            $user->$key = $input;
        }

        return response()->success($user->save());
    }
}
