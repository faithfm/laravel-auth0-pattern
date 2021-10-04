<?php
/**
 * This file is cloned / force-published from the "auth-laravel-v1" composer package.
 *    WARNING: Local modifications will be overwritten when the package is updated.
 *             See https://github.com/faithfm/auth-laravel-v1 for more details.
 */

// Our CustomUserRepository class adds database persistence (combining Auth0 data with the Users Eloquent model)
//
// Note: CustomUserRepository.php was initially based on Auth0's Laravel Quickstart's "Optional: Custom User Handling" section...   (See May 2020 version of https://auth0.com/docs/quickstart/webapp/laravel#optional-custom-user-handling)
//      ...but the tutorial had errors - see https://github.com/auth0/docs/issues/9002
//      ...and even once the errors were corrected, it returned an Auth0User interface (with a copy of the static "getAttributes()" properties from the User model.  (No access to User methods, etc)
//      Apart from completely changing the way that User data can be accessed for the currently-authenticated user, the Auth0User class is not compatible with other standard features including Laravel's guards implementation.
//      Note: Auth0 are currently looking to rectify this by switching their implementation to use an "Auth0 Trait" (to extend the normal User model instead).  (See https://github.com/auth0/laravel-auth0/pull/165)
//      However until this becomes generally available, I've adapted @aaronflorey's code to create our own Auth0Trait which we add to the User model.   (See https://gist.github.com/aaronflorey/d20f27a2b0475d238e10b46de3bc3eb4)

namespace App\Repositories;

use App\Models\User;

use Auth0\Login\Auth0User;
use Auth0\Login\Auth0JWTUser;
use Auth0\Login\Repository\Auth0UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomUserRepository extends Auth0UserRepository
{

    /**
     * Get an existing user or create a new one
     *
     * @param array $profile - Auth0 profile
     *
     * @return User
     */
    protected function upsertUser($profile)
    {
        return User::firstOrCreate(['sub' => $profile['sub']], [
            'email' => $profile['email'] ?? '',
            'name' => $profile['name'] ?? '',
        ])->load('permissions');
    }

    /**
     * Authenticate a user with a decoded ID Token
     *
     * @param array $decodedJwt
     *
     * @return Auth0JWTUser
     */
    public function getUserByDecodedJWT(array $decodedJwt): Authenticatable
    {
        $user = $this->upsertUser((array) $jwt);
        return new Auth0JWTUser($user->getAttributes());
    }

    /**
     * Get a User from the database using Auth0 profile information
     *
     * @param array $userinfo
     *
     * @return Auth0User
     */
    public function getUserByUserInfo(array $userinfo): Authenticatable
    {
        $user = $this->upsertUser($userinfo['profile']);
        return $user->setAccessToken($userinfo['accessToken'] || '');                       // @aaronflorey's solution which returns a normal User model...
        // return new Auth0User( $user->getAttributes(), $userinfo['accessToken'] );        // ...instead of the original code from Auth0 quickstart tutorial - which returns a non-standard Auth0User class
    }
}
