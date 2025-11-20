<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menangani percobaan autentikasi.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        // 1. Validasi input dari form
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Ambil nilai "Ingat Saya"
        $remember = $request->boolean('remember');

        // 3. Coba loginkan pengguna
        if (Auth::attempt($credentials, $remember)) {
            // 4. Jika berhasil, regenerasi session
            $request->session()->regenerate();

            // 5. Ambil nama pengguna yang login
            $name = Auth::user()->name;

            // 6. Kirim flash message untuk pop-up selamat datang
            $request->session()->flash('status', "Selamat datang kembali, **$name**! 👋");

            // 7. Arahkan ke beranda
            return redirect()->intended('beranda');
        }

        // 8. Jika gagal, kembali ke halaman login dengan pesan error
        return back()->with('error', 'Email atau password yang Anda masukkan salah.');
    }

    /**
     * Men-logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}