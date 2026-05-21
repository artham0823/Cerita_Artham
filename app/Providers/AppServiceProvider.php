<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bagikan variabel $navItems ke semua views secara otomatis
        // biar gak usah di-pass dari tiap controller (menghindari error Undefined variable)
        // pake scope active() — cuma tampilkan yang is_active = true, urut sort_order
        \Illuminate\Support\Facades\View::share('navItems', \App\Models\NavbarItem::active()->get());
    }
}
