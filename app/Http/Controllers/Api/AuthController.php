<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
class AuthController extends Controller
{
    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate(); 
        return response()->json(Auth::user());
    }

    /**
     * Handle a logout request.
     */
    public function logout(Request $request): JsonResponse
    {
        // Use the 'web' guard which is the default for SPA authentication
        Auth::guard('web')->logout();

        // Invalidate the user's session.
        $request->session()->invalidate();

        // Regenerate the CSRF token.
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }
    // public function logout(Request $request)
    // {
    //     // Revoke the token that was used to authenticate the current request...
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }
}