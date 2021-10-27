<?php

use Illuminate\Support\Facades\Route;

// Auth0-related routes.   (Replaces Laravel's default auth routes - normally added with a "Auth::routes();" statement.)
Route::middleware(['web'])->group(function () {
  Route::get('/login',                      'FaithFM\Auth0Pattern\Auth0PatternController@login')   ->name('login');
  Route::get('/auth0/callback',             'FaithFM\Auth0Pattern\Auth0PatternController@callback')->name('auth0-callback');
  Route::match(['get', 'post'], '/logout',  'FaithFM\Auth0Pattern\Auth0PatternController@logout')  ->name('logout')->middleware('auth');
  Route::get('/profile',                    'FaithFM\Auth0Pattern\Auth0PatternController@profile') ->name('profile')->middleware('auth');
});


