<?php

namespace App\Http\Middleware;

use App\Exceptions\TelescopeAuthFailureException;
use Closure;
use Illuminate\Http\Request;

class TelescopeAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  string  $configKey
     * @return mixed
     *
     * @throws TelescopeAuthFailureException
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $this->isAuthenticated($request)) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Telescope"');
            exit;
        }

        return $next($request);
    }

    public function isAuthenticated(Request $request): bool
    {
        $username = config('telescope.auth.username');
        $password = config('telescope.auth.password');

        header('Cache-Control: no-cache, must-revalidate, max-age=0');

        $hasCreds = ! (empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $isAuthenticated = (
            $hasCreds &&
            $_SERVER['PHP_AUTH_USER'] === $username &&
            $_SERVER['PHP_AUTH_PW'] === $password
        );

        return $isAuthenticated;
    }
}
