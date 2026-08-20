<?php

namespace App\Http\Controllers;

use App\Services\CareerApiClient;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('career_access_token')) {
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

    public function logout(Request $request, CareerApiClient $api)
    {
        $token = session('career_access_token');

        if ($token) {
            // Cabut token aktif di sisi api-server. Abaikan kalau gagal (mis. sudah expired).
            try {
                $api->postAuthenticated('/logout', $token);
            } catch (\Throwable $e) {
                // sengaja diabaikan — tetap lanjut logout lokal
            }
        }

        session()->forget(['career_access_token', 'career_user', 'career_token_expires_at']);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}