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
        $locale = $request->header('X-Locale') 
            ?? $request->query('locale')
            ?? ($request->user()?->locale)
            ?? config('app.locale', 'en');

        if (in_array($locale, ['en', 'ur'])) {
            App::setLocale($locale);
        }

        $response = $next($request);

        $response->headers->set('Content-Language', App::getLocale());

        return $response;
    }
}
