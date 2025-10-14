<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo(): string
    {
        if (request()->routeIs('e-billing.*')) {
            return '/e-billing';
        }

        return '/dashboard';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (request()->routeIs('e-billing.*')) {
            $this->middleware('guest:ebil')->except('logout');
            $this->middleware('auth:ebil')->only('logout');
        } else {
            $this->middleware('guest')->except('logout');
            $this->middleware('auth')->only('logout');
        }
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        if (request()->routeIs('e-billing.*')) {
            return view('e-billing::auth.login');
        }

        return view('auth.login');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        if ($request->wantsJson()) {
            return new JsonResponse([], 204);
        }

        return request()->routeIs('e-billing.*')
            ? redirect()->route('e-billing.login')
            : redirect()->route('login');
    }

    protected function guard(): \Illuminate\Contracts\Auth\StatefulGuard
    {
        if (request()->routeIs('e-billing.*')) {
            return Auth::guard('ebil');
        }

        return Auth::guard();
    }
}
