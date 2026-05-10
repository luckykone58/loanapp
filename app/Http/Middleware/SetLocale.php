<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang');
        if (is_string($locale) && $locale !== '') {
            session(['app_locale' => $locale]);
        } else {
            $locale = session('app_locale');
        }

        if (!$locale) {
            // Pull default locale from settings (scoped by domain), fallback to config
            try {
                $settingLocale = \App\Models\Setting::query()
                    ->where('name', 'default_locale')
                    ->value('value');
                if (is_string($settingLocale) && $settingLocale !== '') {
                    $locale = $settingLocale;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        App::setLocale($locale ?: config('app.locale'));

        return $next($request);
    }
}


