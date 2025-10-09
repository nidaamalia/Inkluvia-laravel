<?php

namespace App\Providers;

use App\View\Components\Badge;
use App\View\Components\Detail;
use App\View\Components\StatusRow;
use Illuminate\Support\Facades\Blade;
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
        Blade::component('detail', Detail::class);
        Blade::component('status-row', StatusRow::class);
        Blade::component('badge', Badge::class);
    }
}
