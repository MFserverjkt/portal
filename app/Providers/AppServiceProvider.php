<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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

        // KODE AUTO-LOGIN DIHAPUS TOTAL AGAR SISTEM LOGIN NORMAL
    }
}