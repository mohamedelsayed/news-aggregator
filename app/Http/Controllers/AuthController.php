<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Example for response examples value"
 * )
 */
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

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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

    // Logout
    public function logout(Request $request)
    {
        // Revoke all tokens for this user
        $request->user()->tokens()->delete();

        $data = [];
        $message = 'Logged out successfully';

        return $this->sendResponse($data, $message, Response::HTTP_OK);
    }

    // Forgot password
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
}
