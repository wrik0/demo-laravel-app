<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = User::create([
            "name" => $request->input("name"),
            "email" => $request->input("email"),
            "admin" => false,
            "password" => bcrypt($request->input("password"))
        ]);
        $token = $user->createToken("main")->plainTextToken;

        return response()->json([
            "user" => $user,
            "token" => $token
        ]);
    }
    public function login(Request $request)
    {
        $credentials = $request->validate(([
            "email" => "required|email|string",
            "password" => "string",
            "remember" => "boolean"
        ]));

        $remember = $credentials["remember"] ?? false;

        if (!Auth::attempt($credentials, $remember)) {
            return response()->json([
                "status" => "Unauthorized",
                "message" => "Invalid Credentials"
            ], Response::HTTP_FORBIDDEN);
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->createToken("main")->plainTextToken;

        return response()->json([
            "user" => $user,
            "token" => $token
        ]);
    }
    public function logout()
    {
        try {
            /** @var \App\Models\User  */
            $user = request()->user();
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed to remove token'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
