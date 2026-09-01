<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function register(): View
    {
        return view('auth.register');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $data = $request->validate(['email' => ['required', 'email', 'max:255'], 'password' => ['required', 'string']]);
        $key = 'login:'.hash('sha256', $data['email'].'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.']);
        }
        if (! Auth::attempt($data)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak cocok. Periksa kembali.']);
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();
        if ($request->user()->isAdmin()) {
            $request->session()->forget('url.intended');

            return redirect()->route('admin.dashboard');
        }
        if (str_contains((string) $request->session()->get('url.intended'), '/admin')) {
            $request->session()->forget('url.intended');
        }

        return redirect()->intended(route('reservations.create'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ]);
        $user = User::create([...$data, 'role_id' => Role::where('name', 'customer')->sole()->id]);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('reservations.create')->with('success', 'Akun berhasil dibuat. Pilih jadwal kunjungan Anda.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda sudah keluar.');
    }
}
