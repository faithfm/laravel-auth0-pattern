<?php
/**
 * This file is cloned / force-published from the "laravel-auth0-pattern" composer package.
 *    WARNING: Local modifications will be overwritten when the package is updated.
 *             See https://github.com/faithfm/laravel-auth0-pattern for more details.
 */

namespace App\Models;

use Auth0\Laravel\Contract\Model\Stateful\User as StatefulUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

// Note: auth0/login v7's new methodology elimintates the need for our old Auth0PatternUserModelTrait.
//    Apart from the "StatefulUser" interface implemented here, most Auth0-related functionality is now covered in our Auth0PatternUserRepository.php class.

class User extends \Illuminate\Database\Eloquent\Model implements StatefulUser, AuthenticatableUser, Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable, Authenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'sub',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the permissions for the user.
     */
    public function permissions()
    {
        return $this->hasMany(\App\Models\UserPermission::class);
    }
}
