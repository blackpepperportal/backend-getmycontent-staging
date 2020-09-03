<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'user' , 'middleware' => 'cors'], function() {

	/***
	 *
	 * User Account releated routs
	 *
	 */

    Route::post('register','UserApi\AccountApiController@register');
    
    Route::post('login','UserApi\AccountApiController@login');

    Route::post('forgot_password', 'UserApi\AccountApiController@forgot_password');

    Route::group(['middleware' => 'UserApiVal'] , function() {

        Route::post('profile','UserApi\AccountApiController@profile'); // 1

        Route::post('update_profile', 'UserApi\AccountApiController@update_profile'); // 2

        Route::post('change_password', 'UserApi\AccountApiController@change_password'); // 3

        Route::post('delete_account', 'UserApi\AccountApiController@delete_account'); // 4

        Route::post('logout', 'UserApi\AccountApiController@logout'); // 7

        Route::post('push_notification_update', 'UserApi\AccountApiController@push_notification_status_change');  // 5

        Route::post('email_notification_update', 'UserApi\AccountApiController@email_notification_status_change'); // 6

        Route::post('notifications_status_update','UserApi\AccountApiController@notifications_status_update');


    });

    // Cards management start

    Route::post('cards_add', 'UserApi\AccountApiController@cards_add'); // 15

    Route::post('cards_list', 'UserApi\AccountApiController@cards_list'); // 16

    Route::post('cards_delete', 'UserApi\AccountApiController@cards_delete'); // 17

    Route::post('cards_default', 'UserApi\AccountApiController@cards_default'); // 18

    Route::post('payment_mode_default', 'UserApi\AccountApiController@payment_mode_default');

});