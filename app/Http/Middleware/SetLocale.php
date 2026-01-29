<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from session or default to 'id'
        $locale = Session::get('locale', config('app.locale', 'id'));
        
        // Validate locale
        if (!in_array($locale, ['id', 'en'])) {
            $locale = 'id';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
