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

    Route::get('get_settings_json', function () {

        if(\File::isDirectory(public_path(SETTINGS_JSON))){

        } else {

            \File::makeDirectory(public_path('default-json'), 0777, true, true);

            \App\Helpers\Helper::settings_generate_json();
        }

        $jsonString = file_get_contents(public_path(SETTINGS_JSON));

        $data = json_decode($jsonString, true);

        return $data;
    
    });

	/***
	 *
	 * User Account releated routs
	 *
	 */

    Route::post('register','Api\UserAccountApiController@register');
    
    Route::post('login','Api\UserAccountApiController@login');

    Route::post('forgot_password', 'Api\UserAccountApiController@forgot_password');

    Route::post('regenerate_email_verification_code', 'Api\UserAccountApiController@regenerate_email_verification_code');

    Route::post('verify_email', 'Api\UserAccountApiController@verify_email');

    Route::any('static_pages_web', 'ApplicationController@static_pages_web');

    Route::get('pages/list', 'ApplicationController@static_pages_api');
    
    Route::group(['middleware' => 'UserApiVal'] , function() {

        Route::post('profile','Api\UserAccountApiController@profile');

        Route::post('update_profile', 'Api\UserAccountApiController@update_profile');

        Route::post('change_password', 'Api\UserAccountApiController@change_password');

        Route::post('delete_account', 'Api\UserAccountApiController@delete_account');

        Route::post('logout', 'Api\UserAccountApiController@logout');

        Route::post('push_notification_update', 'Api\UserAccountApiController@push_notification_status_change');

        Route::post('email_notification_update', 'Api\UserAccountApiController@email_notification_status_change');

        Route::post('notifications_status_update','Api\UserAccountApiController@notifications_status_update');

        // Cards management start

        Route::post('cards_add', 'Api\UserAccountApiController@cards_add');

        Route::post('cards_list', 'Api\UserAccountApiController@cards_list');

        Route::post('cards_delete', 'Api\UserAccountApiController@cards_delete');

        Route::post('cards_default', 'Api\UserAccountApiController@cards_default');

        Route::post('payment_mode_default', 'Api\UserAccountApiController@payment_mode_default');

    });

    Route::post('admin_account_details','Api\WalletApiController@admin_account_details');

    Route::group(['middleware' => ['UserApiVal']], function() {

        Route::post('wallets_index','Api\WalletApiController@user_wallets_index');

        Route::post('wallets_add_money_by_stripe', 'Api\WalletApiController@user_wallets_add_money_by_stripe');

        Route::post('wallets_add_money_by_bank_account','Api\WalletApiController@user_wallets_add_money_by_bank_account');
       
        Route::post('wallets_history','Api\WalletApiController@user_wallets_history');

        Route::post('wallets_history_for_add','Api\WalletApiController@user_wallets_history_for_add');

        Route::post('wallets_history_for_sent','Api\WalletApiController@user_wallets_history_for_sent');

        Route::post('wallets_history_for_received','Api\WalletApiController@user_wallets_history_for_received');

        Route::post('wallets_payment_view','Api\WalletApiController@user_wallets_payment_view');

        Route::post('wallets_send_money','Api\WalletApiController@user_wallets_send_money');

        Route::post('subscriptions_index','Api\SubscriptionApiController@subscriptions_index');

        Route::post('subscriptions_view','Api\SubscriptionApiController@subscriptions_view');

        Route::post('subscriptions_payment_by_card','Api\SubscriptionApiController@subscriptions_payment_by_card');

        Route::post('subscriptions_history','Api\SubscriptionApiController@subscriptions_history');

        Route::post('subscriptions_autorenewal_status','Api\SubscriptionApiController@subscriptions_autorenewal_status');

    });

    Route::group(['middleware' => ['IsContentCreator']], function() {

        Route::post('user_products','Api\UserProductApiController@user_products_index');

        Route::post('user_products_save','Api\UserProductApiController@user_products_save');

        Route::post('user_products_view','Api\UserProductApiController@user_products_view');

        Route::post('user_products_delete','Api\UserProductApiController@user_products_delete');

        Route::post('user_products_set_visibility','Api\UserProductApiController@user_products_set_visibility');

        Route::post('user_products_update_availability','Api\UserProductApiController@user_products_update_availability');

        Route::post('product_categories','Api\UserProductApiController@product_categories');

        Route::post('product_sub_categories','Api\UserProductApiController@product_sub_categories');

        Route::post('user_products_search','Api\UserProductApiController@user_products_search');

    });


    Route::post('follow_users','Api\FollowersApiController@follow_users');

    Route::post('unfollow_users','Api\FollowersApiController@unfollow_users');

});