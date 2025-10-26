<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // redirect authenticated users to the respective dashboard
        $middleware->redirectUsersTo(function ($request) {
            if ($request->routeIs('e-billing.*')) {
                return route('e-billing.dashboard');
            }

            return route('dashboard');
        });

        $middleware->alias([
            'auth_ebil' => \Modules\EBilling\Http\Middleware\UserAuthenticate::class,
            'auth.telescope' => \App\Http\Middleware\TelescopeAuthMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->web([
            \App\Http\Middleware\ThemeConfigMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // generate bills daily at midnight
        $schedule->command('e-billing:create-bills')
            ->timezone('Asia/Jakarta')
            ->daily()
            ->withoutOverlapping()
            ->storeOutput();

        // send billing notifications every day at 8 AM
        $schedule->command('e-billing:check-bills')
            ->timezone('Asia/Jakarta')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->storeOutput();
    })
    ->create();
