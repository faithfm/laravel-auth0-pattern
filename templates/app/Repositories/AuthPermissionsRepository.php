<?php

namespace App\Repositories;

class AuthPermissionsRepository
{
    /**
     * The list of permissions recognised by the application
     *
     * Gates are automatically created for all permissions defined here.
     * See: AuthLaravelServiceProvider
     *
     * @var array
     */
    public const DEFINED_PERMISSIONS = [
        'use-app',                  // minimum permission to use the app
        'admin-app',                // master admin privilege
    //  'edit-catalog',             // for catalog editors  (assuming you're writing a catalogue application)
    ];

}
