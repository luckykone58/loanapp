<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Prepend active theme's view path so it overrides default views
        $this->app->booted(function (): void {
            // Admin should render from resources/views (no theme override)
            $request = request();
            if ($request && ($request->is('admin') || $request->is('admin/*'))) {
                return;
            }

            // Resolve theme per current domain (uses App\Support\Settings domain scoping)
            try {
                $theme = \App\Support\Settings::get('theme', 'default');
            } catch (\Throwable $e) {
                $theme = 'default';
            }

            // New templates location at project root: templates/{theme}
            $themePath = base_path("templates/{$theme}");
            $defaultPath = base_path("templates/default");

            // Prepend theme path so it takes precedence, then default fallback
            if (is_dir($defaultPath)) {
                View::getFinder()->prependLocation($defaultPath);
            }
            if (is_dir($themePath)) {
                View::getFinder()->prependLocation($themePath);
            }
        });
    }
}


