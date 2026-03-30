<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // ✅ ADD THIS

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // ✅ FIX: Hash password before saving
        $data['password'] = Hash::make($data['password']);

        $user  = User::create($data);
        $token = $user->createToken('booktick')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

           // ← add this block
    if ($user->is_banned) {
        Auth::logout();
        return response()->json(['message' => 'Your account has been suspended.'], 403);
    }

    $token = $user->createToken('booktick')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $user]);
}

    public function logout(Request $request)
    {
        // ✅ safer logout
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}