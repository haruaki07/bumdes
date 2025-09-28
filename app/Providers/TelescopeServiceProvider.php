<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return
                $entry->type === EntryType::SCHEDULED_TASK ||
                $entry->type === EntryType::JOB ||
                $entry->type === EntryType::EVENT ||
                $entry->type === EntryType::EXCEPTION ||
                $entry->type === EntryType::LOG ||
                $entry->type === EntryType::MAIL ||
                $entry->type === EntryType::NOTIFICATION ||
                $isLocal;
        });
    }

    public function boot(): void
    {
        // handle authentication via custom middleware
        // look at app/Http/Middleware/TelescopeAuthMiddleware.php
        Telescope::auth(fn ($request) => true);
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }
}
