<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('phone_number', $request->phone_number)->first();
        if ($user && Hash::check($request->pin, $user->pin)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'token'      => $token,
                'user'       => new UserResource($user),
                'expires_in' => 1440 * 60, // 24 hours in seconds
            ]);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    /**
     * Refresh token for authenticated user.
     */
    public function refresh()
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();
        $newToken = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'token'      => $newToken,
            'user'       => new UserResource($user),
            'expires_in' => 1440 * 60, // 24 hours in seconds
        ]);
    }
}
