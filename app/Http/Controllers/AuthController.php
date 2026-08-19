<?php

namespace App\Http\Controllers;

use App\Services\CareerApiClient;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('career_access_token')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request, CareerApiClient $api)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $response = $api->postRaw('/login', [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'device_name' => 'dashboard-'.$request->ip(),
        ]);

        if ($response->status() === 401 || $response->status() === 422) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak valid.',
            ]);
        }

        $response->throw(); // lempar error kalau selain itu (500, dll)

        $data = $response->json();

        // Simpan sesi login berbasis Career API — bukan Auth::attempt() lokal.
        session([
            'career_access_token' => $data['access_token'],
            'career_user' => $data['user'],
            'career_token_expires_at' => $data['expires_at'] ?? null,
        ]);

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