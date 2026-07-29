<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BypassLogin
{
    public function handle(Request $request, Closure $next)
    {
        // Jika belum ada yang login, otomatis loginkan user ADMIN pertama di database
        if (!Auth::check()) {
            $adminUser = User::where('role', 'ADMIN')->first() ?? User::first();
            if ($adminUser) {
                Auth::login($adminUser);
            }
        }

        return $next($request);
    }
}