<?php

namespace App\Providers;

use App\Mail\Transport\GmailApiTransport;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('pagination.site');

        // Gmail API instead of SMTP: Railway blocks outgoing SMTP ports.
        Mail::extend('gmail-api', function (array $config) {
            return new GmailApiTransport(
                $config['client_id'],
                $config['client_secret'],
                $config['refresh_token'],
            );
        });
    }
}
