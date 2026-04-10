<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // 1. أضف هذا السطر هنا

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 2. أضف هذا الشرط هنا
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}