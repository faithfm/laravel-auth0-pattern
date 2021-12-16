<?php

namespace FaithFM\Auth0Pattern\Http\Middleware;

use Closure;
use Illuminate\Session\Middleware\StartSession as BaseStartSession;

/**
 * Define our own StartSession class, that prevents sessions from being created when request contains 'api_token=XXXX'
 * Adapted from: https://stackoverflow.com/a/29251516
 */
class StartSession extends BaseStartSession
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\Request::has('api_token'))
        {
            \Config::set('session.driver', 'array');
            \Config::set('cookie.driver', 'array');
        }
        return parent::handle($request, $next);
    }
}

