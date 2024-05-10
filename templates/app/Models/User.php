<?php

/**
 * This file is cloned / force-published from the "laravel-auth0-pattern" composer package.
 *    WARNING: Local modifications will be overwritten when the package is updated.
 *             See https://github.com/faithfm/laravel-auth0-pattern for more details.
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

// Model signature is based on a combination of:
//  * Laravel (v10) default User model
//  * A non-password-based version of it's parent (Illuminate\Foundation\Auth\User)
//  * Our own Auth0 pattern and the documentation in its child packages: 
//    * laravel-simple-auth0
//    * laravel-simple-auth-tokens
//    * laravel-simple-permissions

class User extends Model implements AuthenticatableContract, AuthorizableContract, Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable, Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'sub',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * The permissions relationship should be eager-loaded.
     *
     * @var array
     */
    protected $with = ['permissions'];

    /**
     * Get the permissions for the user.
     * See: faithfm/laravel-simple-permissions package
     */
    public function permissions()
    {
        return $this->hasMany(\App\Models\UserPermission::class);
    }
}
