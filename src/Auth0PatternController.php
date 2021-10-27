<?php

namespace FaithFM\Auth0Pattern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class Auth0PatternController extends Controller
{
    /**
     * Redirect to the Auth0 hosted login page
     *
     * @return mixed
     */
    public function login()
    {
        $authorize_params = [
            'scope' => 'openid profile email',
            // Use the key below to get an access token for your API.
            // 'audience' => config('laravel-auth0.api_identifier'),
        ];
        return app('auth0')->login(null, null, $authorize_params);
    }

    
    /**
     * Handle Auth0 callback redirect (after login)... and redirect back to app.
     *
     * @return mixed
     */
    public function callback()
    {
        // Auth0's Laravel library has already defined this controller method for us
        return app()->call('Auth0\Login\Auth0Controller@callback');
    }


    /**
     * Log out of Auth0
     *
     * @return mixed
     */
    public function logout()
    {
        Auth::logout();
        $logoutUrl = sprintf(
            'https://%s/v2/logout?client_id=%s&returnTo=%s',
            config('laravel-auth0.domain'),
            config('laravel-auth0.client_id'),
            url('/')
        );
        return  Redirect::intended($logoutUrl);
    }

     /**
     * Display the user's Auth0 data
     *
     * @return mixed
     */
    public function profile()
    {
        if ( ! Auth::check() ) {
            return redirect()->route('login');
        } else {
            return '<pre class="text-left">' . json_encode( Auth::user(), JSON_PRETTY_PRINT ) .'</pre>';
        }

    }
}
