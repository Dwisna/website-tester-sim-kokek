<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

        public function login(Request $request)
        {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);
            //nambah ini 18 agus
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && $user->expired_at && now()->greaterThan($user->expired_at)) {
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda sudah tidak aktif.',
                ]);
            }
            //sampai sini
            if (!Auth::attempt($credentials, $request->boolean('remember'))) {
                throw ValidationException::withMessages([
                    'email' => 'Email atau password tidak valid.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus session lama dan buat CSRF token baru.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
