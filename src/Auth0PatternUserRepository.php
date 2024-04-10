<?php

/**
 * Our Auth0PatternUserRepository class adds database persistence (combining Auth0 data with the Eloquent User model)
 *
 * Note: Based on a combination of:
 *  - Auth0's v7.12 Eloquent documentation (https://github.com/auth0/laravel-auth0/blob/main/docs/Eloquent.md) which has a more complex version of this pattern
 *  - Our own existing code (from previous versions of this library/pattern)
 *  - Note: we DON'T follow Auth0's recommendation of using their SDK as the master data source and syncing the DB.  (AuthZ permissions are different for each application + no need for vendor lock-in)
 *  - Additionally, we memory-cache the retrieved User model in $this->userModel to prevent the DB being hit multiple times
 */

declare(strict_types=1);

namespace FaithFM\Auth0Pattern;

use App\Models\User;
use Auth0\Laravel\{UserRepositoryAbstract, UserRepositoryContract};
use Illuminate\Contracts\Auth\Authenticatable;

final class Auth0PatternUserRepository extends UserRepositoryAbstract implements UserRepositoryContract
{

    protected $userModel;

    public function fromSession(array $user): ?Authenticatable
    {
        if (!$this->userModel) {
            // lookup user first time function is called then remember to avoid multiple DB calls
            $this->userModel = User::firstOrCreate(['sub' => $user['sub']], [
                'email' => $user['email'] ?? '',
                'name' => $user['name'] ?? '',
            ])->load('permissions');
        }
        return $this->userModel;
    }

    public function fromAccessToken(array $user): ?Authenticatable
    {
        // Simliar to above. Used for stateless application types.
        return null;
    }

}
