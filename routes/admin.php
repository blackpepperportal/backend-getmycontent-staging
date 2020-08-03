<?php

Route::group(['middleware' => 'web'], function() {

    Route::group(['as' => 'admin.', 'prefix' => 'admin'], function(){

        Route::get('clear-cache', function() {

            $exitCode = Artisan::call('config:cache');

            return back();

        })->name('clear-cache');

        Route::get('login', 'Auth\AdminLoginController@showLoginForm')->name('login');

        Route::post('login', 'Auth\AdminLoginController@login')->name('login.post');

        Route::get('logout', 'Auth\AdminLoginController@logout')->name('logout');

        /***
         *
         * Admin Account releated routes
         *
         */

        Route::get('profile', 'AdminController@profile')->name('profile');

        Route::post('profile/save', 'AdminController@profile_save')->name('profile.save');

        Route::post('change/password', 'AdminController@change_password')->name('change.password');

        Route::get('/', 'AdminController@index')->name('dashboard');
        
        // Users CRUD Operations

        Route::get('users', 'AdminController@users_index')->name('users.index');

        Route::get('users/create', 'AdminController@users_create')->name('users.create');

        Route::get('users/edit', 'AdminController@users_edit')->name('users.edit');

        Route::post('users/save', 'AdminController@users_save')->name('users.save');

        Route::get('users/view', 'AdminController@users_view')->name('users.view');

        Route::get('users/delete', 'AdminController@users_delete')->name('users.delete');

        Route::get('users/status', 'AdminController@users_status')->name('users.status');

        Route::get('users/verify', 'AdminController@users_verify_status')->name('users.verify');

        // settings

        Route::get('/admin-control', 'AdminController@admin_control')->name('control');

        Route::get('/ios-control', 'AdminController@ios_control')->name('ios-control'); 

        Route::get('settings', 'AdminController@settings')->name('settings'); 

        Route::post('settings/save', 'AdminController@settings_save')->name('settings.save'); 

        Route::post('env_settings','AdminController@env_settings_save')->name('env-settings.save');

    });
});