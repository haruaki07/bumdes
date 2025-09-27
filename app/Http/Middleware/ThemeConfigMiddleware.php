<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ThemeConfigMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $themeConfig = json_decode($request->cookie('theme-config', '{}'), true);
        View::share('themeConfig', $themeConfig ?? []);

        return $next($request);
    }
}
