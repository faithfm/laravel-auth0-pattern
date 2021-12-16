<?php 

namespace FaithFM\Auth0Pattern;

use Illuminate\Session\SessionServiceProvider as BaseSessionServiceProvider;

/**
 * Define SessionServiceProvider for our own StartSession class
 * Source: https://stackoverflow.com/a/29251516
 */
class SessionServiceProvider extends BaseSessionServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSessionManager();

        $this->registerSessionDriver();

        $this->app->singleton('FaithFM\Auth0Pattern\Http\Middleware\StartSession');
    }
}

