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
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->registerGuard();
    }

    /**
     * Register Gashey Lumen Passport's migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (Passport::$runsMigrations) {
            return $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Register the token repository binding.
     *
     * @return void
     */
    public function registerBindings()
    {
        $this->app->bind(\Laravel\Passport\TokenRepository::class, function ($app) {
            return new TokenRepository();
        });
    }

    /**
     * Register the token guard.
     *
     * @return void
     */
    public function registerGuard()
    {
        Auth::extend('passport', function ($app, $name, array $config) {
            return tap($this->makeGuard($config), function ($guard) {
                $this->app->refresh('request', $guard, 'setRequest');
            });
        });
    }

    /**
     * Make an instance of the token guard.
     *
     * @param  array  $config
     * @return \Illuminate\Auth\RequestGuard
     */
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
