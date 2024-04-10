<?php

/**
 * This file is cloned / force-published from the "laravel-auth0-pattern" composer package.
 *    WARNING: Local modifications will be overwritten when the package is updated.
 *             See https://github.com/faithfm/laravel-auth0-pattern for more details.
 */

 declare(strict_types=1);
 
 use Auth0\Laravel\Configuration;
 use Auth0\SDK\Configuration\SdkConfiguration;
 
 return Configuration::VERSION_2 + [
     // FFM PATTERN NOTE: auth0/login's automatic registration functionality does not work due to compatibility issues with our pattern, so we disable them here...
     'registerGuards' => false,                     // Not compatible with our FFM pattern, however a commented-out version of this is included in our config/auth.php template - to provide visibility of the difference between our configuration compared with Auth0 SDK default guards + providers
     'registerMiddleware' => false,                 // Not compatible with our FFM pattern. These middleware simply try to force the 'auth-session' + 'auth0-api' guards - which is counter-productive with our own guards
     'registerAuthenticationRoutes' => false,       // Replaced by our own route registration per 'ffmRegisterAuthenticationRoutes' setting below.  Routes are defined in: src/routes/auth0pattern-web.php
     'configurationPath' => null,

     // FFM PATTERN: additional config setting
     'ffmRegisterAuthenticationRoutes' => true,     // To replace Auth0 SDK's default 'registerAuthenticationRoutes' implementation which is hard-coded to the 'web' group, and we are using 'web_group' group.
 
     'guards' => [
         'default' => [
             Configuration::CONFIG_STRATEGY => Configuration::get(Configuration::CONFIG_STRATEGY, SdkConfiguration::STRATEGY_NONE),
             Configuration::CONFIG_DOMAIN => Configuration::get(Configuration::CONFIG_DOMAIN),
             Configuration::CONFIG_CUSTOM_DOMAIN => Configuration::get(Configuration::CONFIG_CUSTOM_DOMAIN),
             Configuration::CONFIG_CLIENT_ID => Configuration::get(Configuration::CONFIG_CLIENT_ID),
             Configuration::CONFIG_CLIENT_SECRET => Configuration::get(Configuration::CONFIG_CLIENT_SECRET),
             Configuration::CONFIG_AUDIENCE => Configuration::get(Configuration::CONFIG_AUDIENCE),
             Configuration::CONFIG_ORGANIZATION => Configuration::get(Configuration::CONFIG_ORGANIZATION),
             Configuration::CONFIG_USE_PKCE => Configuration::get(Configuration::CONFIG_USE_PKCE),
             Configuration::CONFIG_SCOPE => Configuration::get(Configuration::CONFIG_SCOPE),
             Configuration::CONFIG_RESPONSE_MODE => Configuration::get(Configuration::CONFIG_RESPONSE_MODE),
             Configuration::CONFIG_RESPONSE_TYPE => Configuration::get(Configuration::CONFIG_RESPONSE_TYPE),
             Configuration::CONFIG_TOKEN_ALGORITHM => Configuration::get(Configuration::CONFIG_TOKEN_ALGORITHM),
             Configuration::CONFIG_TOKEN_JWKS_URI => Configuration::get(Configuration::CONFIG_TOKEN_JWKS_URI),
             Configuration::CONFIG_TOKEN_MAX_AGE => Configuration::get(Configuration::CONFIG_TOKEN_MAX_AGE),
             Configuration::CONFIG_TOKEN_LEEWAY => Configuration::get(Configuration::CONFIG_TOKEN_LEEWAY),
             Configuration::CONFIG_TOKEN_CACHE => Configuration::get(Configuration::CONFIG_TOKEN_CACHE),
             Configuration::CONFIG_TOKEN_CACHE_TTL => Configuration::get(Configuration::CONFIG_TOKEN_CACHE_TTL),
             Configuration::CONFIG_HTTP_MAX_RETRIES => Configuration::get(Configuration::CONFIG_HTTP_MAX_RETRIES),
             Configuration::CONFIG_HTTP_TELEMETRY => Configuration::get(Configuration::CONFIG_HTTP_TELEMETRY),
             Configuration::CONFIG_MANAGEMENT_TOKEN => Configuration::get(Configuration::CONFIG_MANAGEMENT_TOKEN),
             Configuration::CONFIG_MANAGEMENT_TOKEN_CACHE => Configuration::get(Configuration::CONFIG_MANAGEMENT_TOKEN_CACHE),
             Configuration::CONFIG_CLIENT_ASSERTION_SIGNING_KEY => Configuration::get(Configuration::CONFIG_CLIENT_ASSERTION_SIGNING_KEY),
             Configuration::CONFIG_CLIENT_ASSERTION_SIGNING_ALGORITHM => Configuration::get(Configuration::CONFIG_CLIENT_ASSERTION_SIGNING_ALGORITHM),
             Configuration::CONFIG_PUSHED_AUTHORIZATION_REQUEST => Configuration::get(Configuration::CONFIG_PUSHED_AUTHORIZATION_REQUEST),
             Configuration::CONFIG_BACKCHANNEL_LOGOUT_CACHE => Configuration::get(Configuration::CONFIG_BACKCHANNEL_LOGOUT_CACHE),
             Configuration::CONFIG_BACKCHANNEL_LOGOUT_EXPIRES => Configuration::get(Configuration::CONFIG_BACKCHANNEL_LOGOUT_EXPIRES),
         ],
 
         'api' => [
             Configuration::CONFIG_STRATEGY => SdkConfiguration::STRATEGY_API,
         ],
 
         'web' => [
             Configuration::CONFIG_STRATEGY => SdkConfiguration::STRATEGY_REGULAR,
             Configuration::CONFIG_COOKIE_SECRET => Configuration::get(Configuration::CONFIG_COOKIE_SECRET, env('APP_KEY')),
             Configuration::CONFIG_REDIRECT_URI => Configuration::get(Configuration::CONFIG_REDIRECT_URI, env('APP_URL') . '/callback'),
             Configuration::CONFIG_SESSION_STORAGE => Configuration::get(Configuration::CONFIG_SESSION_STORAGE),
             Configuration::CONFIG_SESSION_STORAGE_ID => Configuration::get(Configuration::CONFIG_SESSION_STORAGE_ID),
             Configuration::CONFIG_TRANSIENT_STORAGE => Configuration::get(Configuration::CONFIG_TRANSIENT_STORAGE),
             Configuration::CONFIG_TRANSIENT_STORAGE_ID => Configuration::get(Configuration::CONFIG_TRANSIENT_STORAGE_ID),
         ],
     ],
 
     'routes' => [
         Configuration::CONFIG_ROUTE_INDEX => Configuration::get(Configuration::CONFIG_ROUTE_INDEX, '/'),
         Configuration::CONFIG_ROUTE_CALLBACK => Configuration::get(Configuration::CONFIG_ROUTE_CALLBACK, '/callback'),
         Configuration::CONFIG_ROUTE_LOGIN => Configuration::get(Configuration::CONFIG_ROUTE_LOGIN, '/login'),
         Configuration::CONFIG_ROUTE_AFTER_LOGIN => Configuration::get(Configuration::CONFIG_ROUTE_AFTER_LOGIN, '/'),
         Configuration::CONFIG_ROUTE_LOGOUT => Configuration::get(Configuration::CONFIG_ROUTE_LOGOUT, '/logout'),
         Configuration::CONFIG_ROUTE_AFTER_LOGOUT => Configuration::get(Configuration::CONFIG_ROUTE_AFTER_LOGOUT, '/'),
     ],
 ];
 