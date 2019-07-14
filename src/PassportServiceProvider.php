<?php

namespace Gashey\LumenPassport;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\RequestGuard;
use League\OAuth2\Server\ResourceServer;
use Laravel\Passport\ClientRepository;

class PassportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        $this->registerBindings();
        $this->registerGuard();
    }

    public function register()
    { }

    public function registerBindings()
    {
        $this->app->bind(\Laravel\Passport\TokenRepository::class, function ($app) {
            return new TokenRepository();
        });
    }

    public function registerGuard()
    {
        Auth::extend('passport', function ($app, $name, array $config) {
            return tap($this->makeGuard($config), function ($guard) {
                $this->app->refresh('request', $guard, 'setRequest');
            });
        });
    }

    protected function makeGuard(array $config)
    {
        return new RequestGuard(function ($request) use ($config) {
            return (new TokenGuard(
                $this->app->make(ResourceServer::class),
                $this->app->make(TokenToUserProvider::class),
                $this->app->make(TokenRepository::class),
                $this->app->make(ClientRepository::class),
                $this->app->make('encrypter')
            ))->user($request);
        }, $this->app['request']);
    }
}
