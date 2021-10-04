<?php

use Illuminate\Support\Facades\Route;

// Auth0-related routes.   (Replaces Laravel's default auth routes - normally added with a "Auth::routes();" statement.)
Route::middleware(['web'])->group(function () {
  Route::get('/auth0/callback', 'Auth0\Login\Auth0Controller@callback')->name('auth0-callback');
  Route::get('/login', 'App\Http\Controllers\Auth\Auth0IndexController@login')->name('login');
  Route::match(['get', 'post'], '/logout', 'App\Http\Controllers\Auth\Auth0IndexController@logout')->name('logout')->middleware('auth');
  Route::get('/profile', 'App\Http\Controllers\Auth\Auth0IndexController@profile')->name('profile')->middleware('auth');
});


