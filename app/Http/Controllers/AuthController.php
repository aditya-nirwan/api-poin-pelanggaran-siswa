<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        $userId = $user ? $user->id : null;
        $userName = $user ? ($user->nama ?? '(Nama tidak ditemukan)') : '(Email tidak ditemukan)';
        $userRole = $user ? ($user->role ?? '(Peran tidak ditemukan)') : '(Peran tidak ditemukan)';

        if (!$user || !Hash::check($request->password, $user->password)) {


            return [
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ];
        }

        $token = $user->createToken($user->nama);

        Log::create([
            'user_id' => $userId,
            'jenis_aktivitas' => 'login',
            'aktivitas' => 'User ' . ($userName ?? '(Tidak ditemukan)') . '(' . ($userRole) . ') login ke aplikasi',
            'waktu' => now(),
        ]);

        return [
            'status' => true,
            'token' => $token->plainTextToken,
            'user' => $user,
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'You are logged out.'
        ];
    }
}
