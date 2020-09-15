<?php

Route::group(['middleware' => 'web'], function() {

    Route::group(['as' => 'support_member.', 'prefix' => 'support_member'], function(){

        Route::get('clear-cache', function() {

            $exitCode = Artisan::call('config:cache');

            return back();

        })->name('clear-cache');

        Route::get('login', 'Auth\SupportMemberLoginController@showLoginForm')->name('login');

        Route::post('login', 'Auth\SupportMemberLoginController@login')->name('login.post');

        Route::get('logout', 'Auth\SupportMemberLoginController@logout')->name('logout');

        Route::get('profile', 'SupportMember\SupportMemberAccountController@profile')->name('profile');

        Route::post('profile/save', 'SupportMember\SupportMemberAccountController@profile_save')->name('profile.save');

        Route::post('change/password', 'SupportMember\SupportMemberAccountController@change_password')->name('change.password');

        Route::get('/', 'SupportMember\SupportMemberRevenueController@dashboard')->name('dashboard');
    
    });
    
});