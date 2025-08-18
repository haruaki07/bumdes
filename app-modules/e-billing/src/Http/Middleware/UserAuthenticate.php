<?php

namespace Modules\EBilling\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\EBilling\Enums\UserRole;
use Symfony\Component\HttpFoundation\Response;

class UserAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$restParams): Response
    {
        if (! Auth::guard('ebil')->check() || ($restParams && $request->user()->role !== UserRole::fromString($restParams[0]))) {
            return redirect()->route('e-billing.login')
                ->with('error', 'You must be logged in to access this page.');
        }

        return $next($request);
    }
}
