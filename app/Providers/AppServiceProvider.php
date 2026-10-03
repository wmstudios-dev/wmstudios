<?php

namespace App\Providers;

use App\Mail\Transport\GmailApiTransport;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
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

        // Content of the menu panels that open under the header.
        View::composer('layouts.app', fn ($view) => $view->with('mega', \App\Support\MegaMenu::data()));

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
