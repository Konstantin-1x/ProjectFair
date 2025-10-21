<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale'));

        if (is_string($locale) && in_array($locale, ['en', 'ru'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}


