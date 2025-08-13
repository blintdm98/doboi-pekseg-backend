<?php

namespace App\Providers;

use App\Http\ViewComposers\MenuViewComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;
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
        View::composer(['layouts.sidebar', 'layouts.sidebar-mobile'], MenuViewComposer::class);
        
        // Minden oldal betöltésekor magyar nyelvet állítunk be
        View::composer('*', function ($view) {
            App::setLocale('hu');
        });
    }
}
