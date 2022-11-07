<?php

use Illuminate\Support\Facades\Route;

// Auth0-related routes.   (Replaces Laravel's default auth routes - normally added with a "Auth::routes();" statement.)
// Note: auth0/login v7 includes their own routes (supercedes our old Auth0PatternController ones... although we do lose our /profile route)
Route::middleware(['web'])->group(function () {
    Route::get('/login',                        \Auth0\Laravel\Http\Controller\Stateful\Login::class)->name('login');
    Route::match(['get', 'post'], '/logout',    \Auth0\Laravel\Http\Controller\Stateful\Logout::class)->name('logout');
    Route::get('/auth0/callback',               \Auth0\Laravel\Http\Controller\Stateful\Callback::class)->name('auth0.callback');

});


