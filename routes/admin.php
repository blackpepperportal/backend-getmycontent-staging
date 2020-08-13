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

        //stardoms CRUD Operations

        Route::get('stardoms', 'AdminController@stardoms_index')->name('stardoms.index');

        Route::get('stardoms/create', 'AdminController@stardoms_create')->name('stardoms.create');

        Route::get('stardoms/edit', 'AdminController@stardoms_edit')->name('stardoms.edit');

        Route::post('stardoms/save', 'AdminController@stardoms_save')->name('stardoms.save');

        Route::get('stardoms/view', 'AdminController@stardoms_view')->name('stardoms.view');

        Route::get('stardoms/delete', 'AdminController@stardoms_delete')->name('stardoms.delete');

        Route::get('stardoms/status', 'AdminController@stardoms_status')->name('stardoms.status');

        Route::get('stardoms/verify', 'AdminController@stardoms_verify_status')->name('stardoms.verify');

        //stardom documents 

        Route::get('stardoms/documents', 'AdminController@stardoms_documents_index')->name('stardoms.documents.index');

        Route::get('stardoms/documents/view', 'AdminController@stardoms_documents_view')->name('stardoms.documents.view');

        Route::get('stardoms/documents/verify', 'AdminController@stardoms_documents_verify')->name('stardoms.documents.verify');

        //document CRUD Operations

        Route::get('documents', 'AdminController@documents_index')->name('documents.index');

        Route::get('documents/create', 'AdminController@documents_create')->name('documents.create');

        Route::get('documents/edit', 'AdminController@documents_edit')->name('documents.edit');

        Route::post('documents/save', 'AdminController@documents_save')->name('documents.save');

        Route::get('documents/view', 'AdminController@documents_view')->name('documents.view');

        Route::get('documents/delete', 'AdminController@documents_delete')->name('documents.delete');

        Route::get('documents/status', 'AdminController@documents_status')->name('documents.status');

        Route::get('/static_pages' , 'AdminController@static_pages_index')->name('static_pages.index');

        Route::get('/static_pages/create', 'AdminController@static_pages_create')->name('static_pages.create');

        Route::get('/static_pages/edit', 'AdminController@static_pages_edit')->name('static_pages.edit');

        Route::post('/static_pages/save', 'AdminController@static_pages_save')->name('static_pages.save');

        Route::get('/static_pages/delete', 'AdminController@static_pages_delete')->name('static_pages.delete');

        Route::get('/static_pages/view', 'AdminController@static_pages_view')->name('static_pages.view');

        Route::get('/static_pages/status', 'AdminController@static_pages_status_change')->name('static_pages.status');

        // settings

        Route::get('/admin-control', 'AdminController@admin_control')->name('control');

        Route::get('/ios-control', 'AdminController@ios_control')->name('ios-control'); 

        Route::get('settings', 'AdminController@settings')->name('settings'); 

        Route::post('settings/save', 'AdminController@settings_save')->name('settings.save'); 

        Route::post('env_settings','AdminController@env_settings_save')->name('env-settings.save');

    });
});