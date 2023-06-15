<?php

namespace App\Http\ApiControllers;

use App\Http\Resources\MyUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use function response;

class AuthController extends Controller
{
    public function sendResetPass(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->success()
            : response()->error();
    }

    public function resetPass(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->error();
        }

        $user = User::where('email', $request->get('email'))->first();

        return response()->success([
            'access_token' => $user->createToken('api')->plainTextToken
        ]);
    }

    public function register(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string|min:2|max:50',
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
            $user->save();
        }

        return response()->success([
            'access_token' => $user->createToken('api')->plainTextToken
        ]);
    }

    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email',
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
        auth()->user()->currentAccessToken()->delete();

        return response()->success();
    }

    public function me(Request $request)
    {
        return response()->success(new MyUserResource($request->user()));
    }

    public function refresh(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->success([
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
                $user->savePoster($photo_url);
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
        $googleUser = (object)$request->data;

        $user = User::where('google_id', $googleUser->id)->first();

        if (!$user) {
            $user = User::where('email', $googleUser->email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email
                ]);

                $user->savePoster($googleUser->photoUrl);
            }

            $user->google_id = $googleUser->id;
            $user->save();
        }

        if (!$user->user_name) {
            $user->user_name = 'user' . $user->id;
            $user->save();
        }

        return response()->success([
            'access_token' => $user->createToken('api')->plainTextToken
        ]);
    }
}
