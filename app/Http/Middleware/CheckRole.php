<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        if ($user->role === 'ADMIN') {
            return $next($request);
        }

        // 3. Cek Parameter yang Dikirim
        if (!empty($args)) {
            // A. Jika parameter berupa nama Permission Dinamis (misal: 'users.index' atau 'report.it')
            $hasPermission = DB::table('role_has_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->where('role_has_permissions.role', $user->role)
                ->whereIn('permissions.name', $args)
                ->exists();

            if ($hasPermission) {
                return $next($request);
            }

            // B. Jika parameter berupa daftar Role Hardcoded (misal: 'IT', 'MAINTENANCE', 'OUTLET')
            if (in_array($user->role, $args)) {
                return $next($request);
            }
        }

        // 4. Jika tidak memenuhi kriteria akses
        abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini.');
    }
}