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
            'profile.completed' => \App\Http\Middleware\EnsureProfileIsCompleted::class,
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

        // run automatic backup based on schedule configuration
        if (config('backup.schedule.enabled')) {
            $frequency = config('backup.schedule.frequency', 'weekly');
            $day = config('backup.schedule.day', 0);
            $time = config('backup.schedule.time', '02:00');

            $backupCommand = $schedule->command('backup:run --type=full')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->storeOutput();

            match ($frequency) {
                'daily' => $backupCommand->dailyAt($time),
                'weekly' => $backupCommand->weeklyOn($day, $time),
                'monthly' => $backupCommand->monthlyOn($day, $time),
                default => $backupCommand->weeklyOn($day, $time),
            };
        }

        // clean old backups daily at 3 AM
        if (config('backup.retention.enabled')) {
            $schedule->command('backup:clean --force')
                ->timezone('Asia/Jakarta')
                ->dailyAt('03:00')
                ->withoutOverlapping()
                ->storeOutput();
        }

        $schedule->command('interest:record')
            ->monthlyOn(28, '23:00')
            ->timezone('Asia/Jakarta');
    })
    ->create();
