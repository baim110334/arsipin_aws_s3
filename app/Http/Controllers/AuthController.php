<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Tampilkan Halaman Form Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Proses Validasi Logika Login
    public function login(Request $request)
    {
        // 🌟 VALIDASI DIUBAH: Menjadi string username, bukan email lagi
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba cocokan ke database users
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Cek role pengguna setelah sukses login
            $role = Auth::user()->role;
            
            if ($role === 'admin') {
                return redirect()->intended('/dashboard');
            } elseif ($role === 'pegawai-retail' || $role === 'pegawai') { // Mengantisipasi jika nama role kamu 'pegawai'
                return redirect()->intended('/dashboard/retail');
            } elseif ($role === 'pegawai-komersial') {
                return redirect()->intended('/dashboard/commercial');
            } elseif ($role === 'kepala-bu') {
                return redirect()->route('kepala-bu.dashboard');
            }
        }

        // 🌟 JIKA GAGAL: Kembalikan dengan mempertahankan input username sebelumnya
        return back()->withErrors([
            'loginError' => 'Username atau password yang Anda masukkan salah!',
        ])->onlyInput('username');
    }

    // 3. Proses Keluar Sistem (Logout)
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}