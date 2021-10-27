<?php
/**
 * This file was templated from the "laravel-auth0-pattern" composer package.
 * It should be updated with your own list of permissions used by your application.
 * See: https://github.com/faithfm/laravel-auth0-pattern for more details.
 */

namespace App\Repositories;

class AuthPermissionList
{
    /**
     * The list of permissions recognised by the application 
     * (as applied from the 'user_permissions' table)
     *
     * Gates are automatically created for all permissions defined here.
     * See: Auth0PatternServiceProvider
     *
     * @var array
     */
    public const DEFINED_PERMISSIONS = [
        'use-app',                  // minimum permission to use the app
        'admin-app',                // master admin privilege
    //  'edit-catalog',             // for catalog editors  (assuming you're writing a catalogue application)
    ];

}
