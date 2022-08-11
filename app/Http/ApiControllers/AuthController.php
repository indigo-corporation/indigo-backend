<?php

namespace App\Http\ApiControllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use function response;

class AuthController extends Controller
{

    /**
     * @OA\Post (
     *     path="/auth/register",
     *     operationId="register",
     *     tags={"Auth"},
     *     summary="Regiser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Access token",
     *         @OA\JsonContent(ref="#/components/schemas/AccessTokenResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
    public function register(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $attr['name'],
            'password' => Hash::make($attr['password']),
            'email' => $attr['email']
        ]);

        return response()->success(
            [
                'access_token' => $user->createToken('API Token')->plainTextToken
            ]
        );
    }

    /**
     * @OA\Post (
     *     path="/auth/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     summary="Login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Access token",
     *         @OA\JsonContent(ref="#/components/schemas/AccessTokenResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string|min:6'
        ]);

        if (!Auth::attempt($attr)) {
            return response()->error([
                'code' => 120,
                'message' => 'Credentials not match'
            ], 401);
        }

        return response()->success([
            'access_token' => auth()->user()->createToken('api')->plainTextToken
        ]);
    }

    /**
     * @OA\Post (
     *     path="/auth/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *     summary="Logout",
     *     security={ {"sanctum": {} }},
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
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->success();
    }

    /**
     * @OA\Get (
     *     path="/auth/me",
     *     operationId="me",
     *     tags={"Auth"},
     *     summary="Get user",
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Access token",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
    public function me(Request $request)
    {
        return response()->success(new UserResource($request->user()));
    }

    /**
     * @OA\Get (
     *     path="/auth/refresh",
     *     operationId="refreshToken",
     *     tags={"Auth"},
     *     summary="RefreshToken",
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Access token",
     *         @OA\JsonContent(ref="#/components/schemas/AccessTokenResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
    public function refresh(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'access_token' => $request->user()->createToken('api')->plainTextToken,
        ]);
    }
}
