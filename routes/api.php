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
        
        Route::post('user_premium_account_check', 'Api\UserAccountApiController@user_premium_account_check');

        Route::post('change_password', 'Api\UserAccountApiController@change_password');

        Route::post('delete_account', 'Api\UserAccountApiController@delete_account');

        Route::post('logout', 'Api\UserAccountApiController@logout');

        Route::post('push_notification_update', 'Api\UserAccountApiController@is_push_notification_change');

        Route::post('email_notification_update', 'Api\UserAccountApiController@is_email_notification_change');

        Route::post('notifications_status_update','Api\UserAccountApiController@notifications_status_update');

        // Cards management start

        Route::post('cards_add', 'Api\UserAccountApiController@cards_add');

        Route::post('cards_list', 'Api\UserAccountApiController@cards_list');

        Route::post('cards_delete', 'Api\UserAccountApiController@cards_delete');

        Route::post('cards_default', 'Api\UserAccountApiController@cards_default');

        Route::post('payment_mode_default', 'Api\UserAccountApiController@payment_mode_default');

        Route::post('documents_list', 'Api\VerificationApiController@documents_list');

        Route::post('documents_save','Api\VerificationApiController@documents_save');

        Route::post('documents_delete','Api\VerificationApiController@documents_delete');

        Route::post('documents_delete_all','Api\VerificationApiController@documents_delete_all');

        Route::post('user_documents_status','Api\VerificationApiController@user_documents_status');

        Route::post('billing_accounts_list','Api\UserAccountApiController@user_billing_accounts_list');

        Route::post('billing_accounts_save','Api\UserAccountApiController@user_billing_accounts_save');

        Route::post('billing_accounts_delete','Api\UserAccountApiController@user_billing_accounts_delete');
        
        Route::post('billing_accounts_default','Api\UserAccountApiController@user_billing_accounts_default');

        // Content Creator profile for other users
        Route::post('content_creators_profile','Api\UserAccountApiController@content_creators_profile');

        Route::post('content_creators_posts','Api\UserAccountApiController@content_creators_posts');

        Route::post('content_creators_post_albums','Api\UserAccountApiController@content_creators_post_albums');

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

        Route::post('subscription_payments_autorenewal','ApplicationController@subscription_payments_autorenewal');

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

        Route::post('user_product_pictures' , 'Api\UserProductApiController@user_product_pictures');

        Route::post('user_product_pictures_save','Api\UserProductApiController@user_product_pictures_save');

        Route::post('user_product_pictures_delete','Api\UserProductApiController@user_product_pictures_delete');

        // Followers and Followings list for content creators
        Route::post('followers', 'Api\FollowersApiController@followers');

        Route::post('followings', 'Api\FollowersApiController@followings');

        Route::post('posts_for_owner','Api\PostsApiController@posts_for_owner');

        Route::post('posts_save_for_owner','Api\PostsApiController@posts_save_for_owner');

        Route::post('posts_view_for_owner','Api\PostsApiController@posts_view_for_owner');

        Route::post('posts_delete_for_owner','Api\PostsApiController@posts_delete_for_owner');

    });


    Route::post('follow_users','Api\FollowersApiController@follow_users');

    Route::post('unfollow_users','Api\FollowersApiController@unfollow_users');


    Route::post('other_profile','Api\UserAccountApiController@other_profile');

    Route::post('other_profile_posts','Api\UserAccountApiController@other_profile_posts');

    Route::post('user_subscriptions','Api\UserAccountApiController@user_subscriptions');

    Route::post('user_subscriptions_payment_by_stripe','Api\UserAccountApiController@user_subscriptions_payment_by_stripe');

    Route::post('user_subscriptions_payment_by_wallet','Api\UserAccountApiController@user_subscriptions_payment_by_wallet');

    Route::post('user_subscriptions_history','Api\UserAccountApiController@user_subscriptions_history');

    Route::post('user_subscriptions_autorenewal','Api\UserAccountApiController@user_subscriptions_autorenewal');


    Route::post('home','Api\PostsApiController@home');

    Route::post('posts_search','Api\PostsApiController@posts_search');

    Route::post('posts_view_for_others','Api\PostsApiController@posts_view_for_others');

    Route::post('user_suggestions','Api\FollowersApiController@user_suggestions');

    Route::post('posts_payment_by_wallet','Api\PostsApiController@posts_payment_by_wallet');

    Route::post('posts_payment_by_stripe','Api\PostsApiController@posts_payment_by_stripe');

    Route::post('post_comments','Api\PostsApiController@post_comments');

    Route::post('post_comments_save','Api\PostsApiController@post_comments_save');
    
    Route::post('post_comments_delete','Api\PostsApiController@post_comments_delete');


    Route::post('post_bookmarks','Api\PostsApiController@post_bookmarks');

    Route::post('post_bookmarks_save','Api\PostsApiController@post_bookmarks_save');
    
    Route::post('post_bookmarks_delete','Api\PostsApiController@post_bookmarks_delete');


    Route::post('post_likes','Api\PostsApiController@post_likes');

    Route::post('post_likes_save','Api\PostsApiController@post_likes_save');
    
    Route::post('post_likes_delete','Api\PostsApiController@post_likes_delete');

    Route::post('fav_users','Api\PostsApiController@fav_users');

    Route::post('fav_users_save','Api\PostsApiController@fav_users_save');
    
    Route::post('fav_users_delete','Api\PostsApiController@fav_users_delete');

    Route::post('post_likes','Api\PostsApiController@post_likes');

    Route::post('post_likes_save','Api\PostsApiController@post_likes_save');
    
    Route::post('post_likes_delete','Api\PostsApiController@post_likes_delete');
    

    Route::post('tips_payment_by_stripe','Api\PostsApiController@tips_payment_by_stripe');

    Route::post('tips_payment_by_wallet','Api\PostsApiController@tips_payment_by_wallet');
});