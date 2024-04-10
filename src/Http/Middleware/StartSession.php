<?php

namespace FaithFM\Auth0Pattern\Http\Middleware;

use Closure;
use Illuminate\Session\Middleware\StartSession as BaseStartSession;

/**
 * Define our own StartSession class, that prevents sessions from being created when request contains 'api_token=XXXX'
 * Adapted from: https://stackoverflow.com/a/29251516
 * 
 * Note: Whenever session-based middleware is triggered, Laravel will automatically start a session for the request.
 *       This is normally ok when calling from a browser (which stores cookies and retains the session next time a call
 *       is made), but when cookies are not enabled a new session will be created for every request - which can result 
 *       in thousands of sessions-files being created in the storage/framework/sessions directory.
 *       This middleware detects the presence of an 'api_token' parameter (which is typical for clients without cookies)
 *       and saves the session to an array instead of the filesystem.  (this prevents the creation of a session file)
 */
class StartSession extends BaseStartSession
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     */
    public function handle($request, Closure $next): mixed
    {
        if(\Request::has('api_token'))
        {
            \Config::set('session.driver', 'array');
            \Config::set('cookie.driver', 'array');
        }
        return parent::handle($request, $next);
    }
}

