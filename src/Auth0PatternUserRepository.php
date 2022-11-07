<?php

/**
 * Our Auth0PatternUserRepository class adds database persistence (combining Auth0 data with the Eloquent User model)
 *
 * Note: Based on a combination of:
 *  - Auth0's v7.2.2 example (https://github.com/auth0/laravel-auth0/blob/main/EXAMPLES.md) which provides the basic overview of the 'new' way of providing a real User model
 *  - AUth0's v7.2.2 README (https://github.com/auth0/laravel-auth0/blob/main/README.md) which provided missing information regarding config/auth.php
 *  - Our own existing code (from v1.0 of this library/pattern)
 *  - Additionally, we store the retrieved User model in $this->userModel to prevent the DB being hit multiple times
 */

declare(strict_types=1);

namespace FaithFM\Auth0Pattern;

use App\Models\User;

class Auth0PatternUserRepository implements \Auth0\Laravel\Contract\Auth\User\Repository
{

    protected $userModel;

    public function fromSession(array $user): ?\Illuminate\Contracts\Auth\Authenticatable {
        if (!$this->userModel) {
            // lookup user first time function is called then remember to avoid multiple DB calls
            $this->userModel = User::firstOrCreate(['sub' => $user['sub']], [
                'email' => $user['email'] ?? '',
                'name' => $user['name'] ?? '',
            ])->load('permissions');
        }
        return $this->userModel;
    }

    public function fromAccessToken(array $user): ?\Illuminate\Contracts\Auth\Authenticatable {
        // Simliar to above. Used for stateless application types.
        return null;
    }

}
