<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Otomatis login-kan user pertama jika belum ada session aktif
        try {
            if (!Auth::check()) {
                $user = User::first();
                if ($user) {
                    Auth::login($user);
                }
            }
        } catch (\Exception $e) {
            // Mengabaikan error jika tabel users belum di-migrate
        }
    }
}