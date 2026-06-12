<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login atau belum. Kalau belum, tendang ke login!
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Ambil role user yang sedang aktif login
        $userRole = Auth::user()->role; 

        // 3. Cek langsung di dalam array $roles yang dikirim dari web.php
        if (in_array($userRole, $roles)) {
            return $next($request); // Lolos!
        }

        // ✨ ANTISIPASI EXTRA: Bersihkan karakter strip/underscore biar toleran
        foreach ($roles as $role) {
            $bersihRole = str_replace('-', '_', $role);
            $bersihUser = str_replace('-', '_', $userRole);
            
            if ($bersihUser === $bersihRole) {
                return $next($request); // Lolos!
            }
        }

        // 4. Jika tidak cocok dengan role apa pun, kunci pintu dan keluarkan 403
        abort(403, 'Akun tidak memiliki akses.');
    }
}