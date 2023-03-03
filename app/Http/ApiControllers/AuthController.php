<?php

namespace App\Http\ApiControllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Laravel\Socialite\Facades\Socialite;

use function response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|unique:users,email',
            'user_name' => 'string|min:2|unique:users,username',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $attr['name'],
            'password' => Hash::make($attr['password']),
            'email' => $attr['email']
        ]);

        if (!$user->user_name) {
            $user->user_name = 'user' . $user->id;
        }

        return response()->success(
            [
                'access_token' => $user->createToken('API Token')->plainTextToken
            ]
        );
    }

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

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->success();
    }

    public function me(Request $request)
    {
        return response()->success(new UserResource($request->user()));
    }

    public function refresh(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'access_token' => $request->user()->createToken('api')->plainTextToken,
        ]);
    }

    public function telegramAuth(Request $request)
    {
        $userData = $this->checkTelegramAuthorization($request->get('data'));

        $user = User::where('telegram_id', $userData['id'])->first();

        if (!$user) {
            $firstName = $userData['first_name'] ?? '';
            $lastName = $userData['last_name'] ?? '';
            $photo_url = $userData['photo_url'] ?? '';
            $name = $lastName
                ? $lastName . ' ' . $firstName
                : $firstName;
            $userName = $userData['username'] ?? '';

            $user = new User();
            $user->name = $name;

            if ($userName && !User::where('user_name', $userName)->exists()) {
                $user->user_name = $userName;
            }

            $user->telegram_id = $userData['id'];

            $user->save();

            if ($photo_url) {
                try {
                    $posterUrl = '/images/user_posters/' . $user->id . '.jpg';
                    Image::make($photo_url)->save(public_path($posterUrl));

                    $user->poster_url = $posterUrl;
                    $user->save();
                } catch (\Throwable $e) {
                }
            }
        }

        if (!$user->user_name) {
            $user->user_name = 'user' . $user->id;
            $user->save();
        }

        return response()->success([
            'access_token' => $user->createToken('api')->plainTextToken
        ]);
    }

    private function checkTelegramAuthorization($auth_data)
    {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);

        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }

        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', env('TELEGRAM_TOKEN'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        if (strcmp($hash, $check_hash) !== 0) {
            throw new \Exception('Data is NOT from Telegram');
        }

        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new \Exception('Data is outdated');
        }

        return $auth_data;
    }

    public function googleAuth(Request $request)
    {
        $user = Socialite::driver('google')->stateless()->userFromToken($request->access_token);

        return $user;
    }
}
