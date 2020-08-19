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

        Route::get('profile', 'Admin\AdminAccountController@profile')->name('profile');

        Route::post('profile/save', 'Admin\AdminAccountController@profile_save')->name('profile.save');

        Route::post('change/password', 'Admin\AdminAccountController@change_password')->name('change.password');

        Route::get('/', 'Admin\AdminRevenueController@main_dashboard')->name('dashboard');
        
        // Users CRUD Operations

        Route::get('users', 'Admin\AdminUserController@users_index')->name('users.index');

        Route::get('users/create', 'Admin\AdminUserController@users_create')->name('users.create');

        Route::get('users/edit', 'Admin\AdminUserController@users_edit')->name('users.edit');

        Route::post('users/save', 'Admin\AdminUserController@users_save')->name('users.save');

        Route::get('users/view', 'Admin\AdminUserController@users_view')->name('users.view');

        Route::get('users/delete', 'Admin\AdminUserController@users_delete')->name('users.delete');

        Route::get('users/status', 'Admin\AdminUserController@users_status')->name('users.status');

        Route::get('users/verify', 'Admin\AdminUserController@users_verify_status')->name('users.verify');

        //stardoms CRUD Operations

        Route::get('stardoms', 'Admin\AdminStardomController@stardoms_index')->name('stardoms.index');

        Route::get('stardoms/create', 'Admin\AdminStardomController@stardoms_create')->name('stardoms.create');

        Route::get('stardoms/edit', 'Admin\AdminStardomController@stardoms_edit')->name('stardoms.edit');

        Route::post('stardoms/save', 'Admin\AdminStardomController@stardoms_save')->name('stardoms.save');

        Route::get('stardoms/view', 'Admin\AdminStardomController@stardoms_view')->name('stardoms.view');

        Route::get('stardoms/delete', 'Admin\AdminStardomController@stardoms_delete')->name('stardoms.delete');

        Route::get('stardoms/status', 'Admin\AdminStardomController@stardoms_status')->name('stardoms.status');

        Route::get('stardoms/verify', 'Admin\AdminStardomController@stardoms_verify_status')->name('stardoms.verify');

        //stardom documents 

        Route::get('stardoms/documents', 'Admin\AdminStardomController@stardoms_documents_index')->name('stardoms.documents.index');

        Route::get('stardoms/documents/view', 'Admin\AdminStardomController@stardoms_documents_view')->name('stardoms.documents.view');

        Route::get('stardoms/documents/verify', 'Admin\AdminStardomController@stardoms_documents_verify')->name('stardoms.documents.verify');

        //stardom products CRUD Operations

        Route::get('stardom_products', 'Admin\AdminStardomController@stardom_products_index')->name('stardom_products.index');

        Route::get('stardom_products/create', 'Admin\AdminStardomController@stardom_products_create')->name('stardom_products.create');

        Route::get('stardom_products/edit', 'Admin\AdminStardomController@stardom_products_edit')->name('stardom_products.edit');

        Route::post('stardom_products/save', 'Admin\AdminStardomController@stardom_products_save')->name('stardom_products.save');

        Route::get('stardom_products/view', 'Admin\AdminStardomController@stardom_products_view')->name('stardom_products.view');

        Route::get('stardom_products/delete', 'Admin\AdminStardomController@stardom_products_delete')->name('stardom_products.delete');

        Route::get('stardom_products/status', 'Admin\AdminStardomController@stardom_products_status')->name('stardom_products.status');

        // Document CRUD Operations

        Route::get('documents', 'Admin\AdminLookupController@documents_index')->name('documents.index');

        Route::get('documents/create', 'Admin\AdminLookupController@documents_create')->name('documents.create');

        Route::get('documents/edit', 'Admin\AdminLookupController@documents_edit')->name('documents.edit');

        Route::post('documents/save', 'Admin\AdminLookupController@documents_save')->name('documents.save');

        Route::get('documents/view', 'Admin\AdminLookupController@documents_view')->name('documents.view');

        Route::get('documents/delete', 'Admin\AdminLookupController@documents_delete')->name('documents.delete');

        Route::get('documents/status', 'Admin\AdminLookupController@documents_status')->name('documents.status');

        // Documents end

        //posts start

        Route::get('/posts' , 'Admin\AdminPostController@posts_index')->name('posts.index');

        Route::get('/posts/delete', 'Admin\AdminPostController@posts_delete')->name('posts.delete');

        Route::get('/posts/view', 'Admin\AdminPostController@posts_view')->name('posts.view');

        Route::get('/posts/status', 'Admin\AdminPostController@posts_status')->name('posts.status');

        Route::get('/post/payments','Admin\AdminRevenueController@post_payments')->name('post.payments');

        Route::get('/post/payments/view','Admin\AdminRevenueController@post_payments_view')->name('post.payments.view');

        //posts end

        //posts albums start

        Route::get('/post_albums' , 'Admin\AdminPostController@post_albums_index')->name('post_albums.index');

        Route::get('/post_albums/delete', 'Admin\AdminPostController@post_albums_delete')->name('post_albums.delete');

        Route::get('/post_albums/view', 'Admin\AdminPostController@post_albums_view')->name('post_albums.view');

        Route::get('/post_albums/status', 'Admin\AdminPostController@post_albums_status')->name('post_albums.status');

        //posts albums end

        //orders start

        Route::get('/orders' , 'Admin\AdminPostController@orders_index')->name('orders.index');

        Route::get('/orders/view', 'Admin\AdminPostController@orders_view')->name('orders.view');

        Route::get('/order/payments','Admin\AdminRevenueController@order_payments')->name('order.payments');

        Route::get('/order/payments/view','Admin\AdminRevenueController@order_payments_view')->name('order.payments.view');

        //orders end

        //revenue dashboard start

        Route::get('/revenues/dashboard','Admin\AdminRevenueController@revenues_dashboard')->name('revenues.dashboard');


        // Static pages start

        Route::get('/static_pages' , 'Admin\AdminLookupController@static_pages_index')->name('static_pages.index');

        Route::get('/static_pages/create', 'Admin\AdminLookupController@static_pages_create')->name('static_pages.create');

        Route::get('/static_pages/edit', 'Admin\AdminLookupController@static_pages_edit')->name('static_pages.edit');

        Route::post('/static_pages/save', 'Admin\AdminLookupController@static_pages_save')->name('static_pages.save');

        Route::get('/static_pages/delete', 'Admin\AdminLookupController@static_pages_delete')->name('static_pages.delete');

        Route::get('/static_pages/view', 'Admin\AdminLookupController@static_pages_view')->name('static_pages.view');

        Route::get('/static_pages/status', 'Admin\AdminLookupController@static_pages_status_change')->name('static_pages.status');

        // Static pages end

        // settings

        Route::get('/admin-control', 'Admin\AdminSettingController@admin_control')->name('control');

        Route::get('settings', 'Admin\AdminSettingController@settings')->name('settings'); 

        Route::post('settings/save', 'Admin\AdminSettingController@settings_save')->name('settings.save'); 

        Route::post('env_settings','Admin\AdminSettingController@env_settings_save')->name('env-settings.save');

    });
});