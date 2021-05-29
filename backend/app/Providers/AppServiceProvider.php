<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Client::creating(function (Client $client) {
            $client->incrementing = false;
            $client->id = Str::uuid()->toString();
        });

        Client::retrieved(function (Client $client) {
            $client->incrementing = false;
        });
    }
}
