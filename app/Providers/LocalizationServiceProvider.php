<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LocalizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use custom application language path at project root: languages/
        $this->app->useLangPath(base_path('languages'));
    }
}




