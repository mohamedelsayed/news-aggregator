<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $data = [
            'user' => $user,
            'token' => $token,
        ];
        $message = 'User registered successfully';

        return $this->sendResponse($data, $message, Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $data = [];
            $message = 'Invalid credentials';

            return $this->sendResponse($data, $message, Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $data = [
            'user' => $user,
            'token' => $token,
        ];
        $message = 'User logged in successfully';

        return $this->sendResponse($data, $message, Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        // Revoke all tokens for this user
        $request->user()->tokens()->delete();

        $data = [];
        $message = 'Logged out successfully';

        return $this->sendResponse($data, $message, Response::HTTP_OK);
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        $data = [];
        $message = __($status);

        return $status === Password::RESET_LINK_SENT
            ? $this->sendResponse($data, $message, Response::HTTP_OK)
            : $this->sendResponse($data, $message, Response::HTTP_BAD_REQUEST);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        $data = [];
        $message = __($status);

        return $status === Password::PASSWORD_RESET
            ? $this->sendResponse($data, $message, Response::HTTP_OK)
            : $this->sendResponse($data, $message, Response::HTTP_BAD_REQUEST);
    }
}
