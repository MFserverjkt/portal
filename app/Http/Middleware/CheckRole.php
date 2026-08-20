<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$args)
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. ADMIN selalu memiliki akses penuh (Superadmin Bypass)
        if ($user->hasRole('ADMIN') || $user->role === 'ADMIN') {
            return $next($request);
        }

        // 3. Cek Parameter yang Dikirim (Role atau Permission)
        if (!empty($args)) {
            // A. Cek jika parameter cocok dengan Permission Spatie user (misal: 'users.index', 'report.it')
            if ($user->hasAnyPermission($args)) {
                return $next($request);
            }

            // B. Cek jika parameter cocok dengan Role Spatie user (misal: 'IT', 'MAINTENANCE', 'OUTLET')
            if ($user->hasAnyRole($args)) {
                return $next($request);
            }

            // C. Kompatibilitas kolom 'role' lama pada tabel users
            if (isset($user->role) && in_array($user->role, $args)) {
                return $next($request);
            }
        }

        // 4. Jika tidak memenuhi kriteria akses
        abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini.');
    }
}