<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Paksa HTTPS jika diakses via Ngrok/Tunneling atau Production
        if (config('app.env') !== 'local' || request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || request()->secure()) {
            URL::forceScheme('https');
        }

        // 2. Otomatis login-kan user pertama jika belum ada session aktif
        try {
            if (!Auth::check()) {
                $user = User::first();
                if ($user) {
                    Auth::login($user);
                }
            }
        } catch (\Exception $e) {
            // Mengabaikan error jika database/tabel users belum di-migrate
        }
    }
}