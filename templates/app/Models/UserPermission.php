<?php
/**
 * This file is cloned / force-published from the "laravel-auth0-pattern" composer package.
 *    WARNING: Local modifications will be overwritten when the package is updated.
 *             See https://github.com/faithfm/laravel-auth0-pattern for more details.
 * 
 *    NOTE:    This file supercedes the UserPermission.php file published by the "faithfm/laravel-simple-permissions" package.
 *             It is identical to the upstream file, except for the inclusion of support for the owen-it/laravel-auditing package.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class UserPermission extends Model implements AuditableContract
{
    use AuditableTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'permission', 'restrictions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'restrictions' => 'array',
    ];

    /**
     * Get the user that owns the permissions.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
