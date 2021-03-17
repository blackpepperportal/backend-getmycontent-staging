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

        $settings_folder = storage_path('public/'.SETTINGS_JSON);


        if(\File::isDirectory($settings_folder)){

        } else {

            \File::makeDirectory($settings_folder, 0777, true, true);

            \App\Helpers\Helper::settings_generate_json();
        }

        $jsonString = file_get_contents(storage_path('app/public/'.SETTINGS_JSON));

        $data = json_decode($jsonString, true);

        return $data;
    
    });

	/***
	 *
	 * User Account releated routs
	 *
	 */

    Route::post('username_validation','Api\UserAccountApiController@username_validation');
    
    Route::any('chat_messages_save', 'ApplicationController@chat_messages_save');

    Route::any('get_notifications_count', 'ApplicationController@get_notifications_count');

    Route::post('register','Api\UserAccountApiController@register');
    
    Route::post('login','Api\UserAccountApiController@login');

    Route::post('forgot_password', 'Api\UserAccountApiController@forgot_password');

    Route::post('reset_password', 'Api\UserAccountApiController@reset_password');


    Route::post('regenerate_email_verification_code', 'Api\UserAccountApiController@regenerate_email_verification_code');

    Route::post('verify_email', 'Api\UserAccountApiController@verify_email');

    Route::any('static_pages_web', 'ApplicationController@static_pages_web');

    Route::any('static_pages', 'ApplicationController@static_pages_api');

    Route::post('chat_users_save', 'Api\UserAccountApiController@chat_users_save');


    Route::group(['middleware' => 'UserApiVal'] , function() {

        Route::post('profile','Api\UserAccountApiController@profile');

        Route::post('update_profile', 'Api\UserAccountApiController@update_profile')->middleware(['CheckEmailVerify']);
            
        Route::post('profile_categories_save','Api\User\UserAccountApiController@profile_categories_save');

        Route::post('user_premium_account_check', 'Api\UserAccountApiController@user_premium_account_check');

        Route::post('change_password', 'Api\UserAccountApiController@change_password');

        Route::post('delete_account', 'Api\UserAccountApiController@delete_account');

        Route::post('logout', 'Api\UserAccountApiController@logout');

        Route::post('push_notification_update', 'Api\UserAccountApiController@is_push_notification_change');

        Route::post('email_notification_update', 'Api\UserAccountApiController@is_email_notification_change');

        Route::post('notifications_status_update','Api\UserAccountApiController@notifications_status_update');

        Route::post('lists_index','Api\UserAccountApiController@lists_index');
        
        Route::post('payments_index','Api\UserAccountApiController@payments_index');
        
        Route::post('bell_notifications_index','Api\UserAccountApiController@bell_notifications_index');

        Route::post('verified_badge_status', 'Api\UserAccountApiController@verified_badge_status');


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

        Route::post('billing_accounts_save','Api\UserAccountApiController@user_billing_accounts_save')->middleware(['CheckEmailVerify']);

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

        // Withdrawls start

        Route::post('withdrawals_index','Api\WalletApiController@user_withdrawals_index');
        
        Route::post('withdrawals_view','Api\WalletApiController@user_withdrawals_view');

        Route::post('withdrawals_search','Api\WalletApiController@user_withdrawals_search');

        Route::post('withdrawals_send_request','Api\WalletApiController@user_withdrawals_send_request');

        Route::post('withdrawals_cancel_request','Api\WalletApiController@user_withdrawals_cancel_request');

        Route::post('withdrawals_check','Api\WalletApiController@user_withdrawals_check');

    });

    Route::group(['middleware' => 'CheckDocumentVerify'] , function() {

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

        Route::post('posts_for_owner','Api\PostsApiController@posts_for_owner');

        Route::post('posts_save_for_owner','Api\PostsApiController@posts_save_for_owner')->middleware(['CheckEmailVerify']);

        Route::post('posts_view_for_owner','Api\PostsApiController@posts_view_for_owner');

        Route::post('posts_delete_for_owner','Api\PostsApiController@posts_delete_for_owner');

        Route::post('post_files_upload','Api\PostsApiController@post_files_upload');

        Route::post('post_files_remove','Api\PostsApiController@post_files_remove');

    });

    Route::group(['middleware' => 'UserApiVal'] , function() {

        // Followers and Followings list for content creators
        Route::post('followers', 'Api\FollowersApiController@followers');

        Route::post('followings', 'Api\FollowersApiController@followings');

        Route::post('active_followers', 'Api\FollowersApiController@active_followers');

        Route::post('expired_followers', 'Api\FollowersApiController@expired_followers');

        Route::post('active_followings', 'Api\FollowersApiController@active_followings');

        Route::post('expired_followings', 'Api\FollowersApiController@expired_followings');


        Route::post('follow_users','Api\FollowersApiController@follow_users');

        Route::post('unfollow_users','Api\FollowersApiController@unfollow_users');

        Route::post('chat_assets', 'Api\ChatApiController@chat_assets_index');

        Route::post('chat_assets_save', 'Api\ChatApiController@chat_assets_save');

        Route::post('chat_assets_delete', 'Api\ChatApiController@chat_assets_delete');
        

        Route::post('chat_assets_payment_by_stripe', 'Api\ChatApiController@chat_assets_payment_by_stripe');

        Route::post('chat_assets_payment_by_wallet', 'Api\ChatApiController@chat_assets_payment_by_wallet');

        Route::post('chat_assets_payment_by_paypal', 'Api\ChatApiController@chat_assets_payment_by_paypal');

        Route::post('chat_asset_payments', 'Api\ChatApiController@chat_asset_payments');

        Route::post('chat_asset_payments_view', 'Api\ChatApiController@chat_asset_payments_view');

        Route::post('chat_users_search','Api\ChatApiController@chat_users_search');

        Route::post('chat_messages_search','Api\ChatApiController@chat_messages_search');

    });

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

    Route::post('users_search', 'Api\FollowersApiController@users_search');

    Route::post('user_suggestions', 'Api\FollowersApiController@user_suggestions');

    Route::post('posts_payment_by_wallet','Api\PostsApiController@posts_payment_by_wallet');

    Route::post('posts_payment_by_stripe','Api\PostsApiController@posts_payment_by_stripe');

    Route::post('post_comments','Api\PostsApiController@post_comments');

    Route::post('post_comments_save','Api\PostsApiController@post_comments_save');
    
    Route::post('post_comments_delete','Api\PostsApiController@post_comments_delete');


    Route::post('post_bookmarks','Api\PostsApiController@post_bookmarks');

    Route::post('post_bookmarks_photos','Api\PostsApiController@post_bookmarks_photos');

    Route::post('post_bookmarks_videos','Api\PostsApiController@post_bookmarks_videos');

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

    Route::post('chat_users','Api\FollowersApiController@chat_users');

    Route::post('chat_messages','Api\FollowersApiController@chat_messages')->middleware(['CheckEmailVerify']);

    Route::post('block_users_save','Api\UserAccountApiController@block_users_save');

    Route::post('block_users','Api\UserAccountApiController@block_users');

    Route::post('report_posts_save','Api\PostsApiController@report_posts_save');

    Route::post('report_posts','Api\PostsApiController@report_posts');


    Route::post('user_subscriptions_payment_by_paypal','Api\UserAccountApiController@user_subscriptions_payment_by_paypal');

    Route::post('tips_payment_by_paypal','Api\PostsApiController@tips_payment_by_paypal');

    Route::post('posts_payment_by_paypal','Api\PostsApiController@posts_payment_by_paypal');

    Route::post('u_categories_list','CategoryCustom\Api\UCategoryApiController@u_categories_list');

    Route::post('u_categories_view','CategoryCustom\Api\UCategoryApiController@u_categories_view');
    
    Route::post('user_tips_history','Api\UserAccountApiController@user_tips_history');

});