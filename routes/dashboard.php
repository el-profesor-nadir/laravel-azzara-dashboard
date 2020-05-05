<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
*/
Route::group([
    'prefix' => '/dashboard',
    'as' => 'dashboard.',
    'namespace' => 'Dashboard',
    'middleware'=> ['auth', 'verified']
    ], function () {

    Route::get('/', 'DashboardController@index')->name('index');

    Route::get('users/{user}/profile','UserProfileController@edit')->name('users.profile.edit');
    Route::post('users/{user}/profile','UserProfileController@update')->name('users.profile.update');

    Route::resource('users', 'UserController');

    Route::resource('permissions', 'PermissionController');

    Route::resource('roles', 'RoleController');

    Route::group(['namespace' => 'Profile'], function() {

        Route::get('/profile', 'ProfileController@index')->name('profile.index');
        Route::post('/profile', 'ProfileController@store')->name('profile.store');

        Route::get('/password', 'PasswordController@index')->name('password.index');
        Route::post('/password', 'PasswordController@store')->name('password.store');

        Route::get('/email', 'EmailUpdateController@index')->name('email.index');
        Route::post('/email', 'EmailUpdateController@store')->name('email.store');

    });

});


