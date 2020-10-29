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

        Route::get('', 'Admin\AdminRevenueController@main_dashboard')->name('dashboard');
        
        // Users CRUD Operations

        Route::get('users', 'Admin\AdminUserController@users_index')->name('users.index');

        Route::get('users/create', 'Admin\AdminUserController@users_create')->name('users.create');

        Route::get('users/edit', 'Admin\AdminUserController@users_edit')->name('users.edit');

        Route::post('users/save', 'Admin\AdminUserController@users_save')->name('users.save');

        Route::get('users/view', 'Admin\AdminUserController@users_view')->name('users.view');

        Route::get('users/delete', 'Admin\AdminUserController@users_delete')->name('users.delete');

        Route::get('users/status', 'Admin\AdminUserController@users_status')->name('users.status');

        Route::get('users/verify', 'Admin\AdminUserController@users_verify_status')->name('users.verify');

        Route::get('users/excel','Admin\AdminUserController@users_excel')->name('users.excel');

      


        //users CRUD Operations

        Route::get('content_creators', 'Admin\AdminContentCreatorController@content_creators_index')->name('content_creators.index');

        Route::get('content_creators/create', 'Admin\AdminContentCreatorController@content_creators_create')->name('content_creators.create');

        Route::get('content_creators/edit', 'Admin\AdminContentCreatorController@content_creators_edit')->name('content_creators.edit');

        Route::post('content_creators/save', 'Admin\AdminContentCreatorController@content_creators_save')->name('content_creators.save');

        Route::get('content_creators/view', 'Admin\AdminContentCreatorController@content_creators_view')->name('content_creators.view');

        Route::get('content_creators/delete', 'Admin\AdminContentCreatorController@content_creators_delete')->name('content_creators.delete');

        Route::get('content_creators/status', 'Admin\AdminContentCreatorController@content_creators_status')->name('content_creators.status');

        Route::get('content_creators/verify', 'Admin\AdminContentCreatorController@content_creators_verify_status')->name('content_creators.verify');

        //user documents 

        Route::get('users/documents', 'Admin\AdminUserController@user_documents_index')->name('users.documents.index');

        Route::get('users/documents/view', 'Admin\AdminUserController@user_documents_view')->name('users.documents.view');

        Route::get('users/documents/verify', 'Admin\AdminUserController@user_documents_verify')->name('users.documents.verify');

        Route::get('users/upgrade_account', 'Admin\AdminUserController@user_upgrade_account')->name('users.upgrade_account');


        //user products CRUD Operations

        Route::get('products', 'Admin\AdminProductController@user_products_index')->name('user_products.index');

        Route::get('products/create', 'Admin\AdminProductController@user_products_create')->name('user_products.create');

        Route::get('products/edit', 'Admin\AdminProductController@user_products_edit')->name('user_products.edit');

        Route::post('products/save', 'Admin\AdminProductController@user_products_save')->name('user_products.save');

        Route::get('products/view', 'Admin\AdminProductController@user_products_view')->name('user_products.view');

        Route::get('products/delete', 'Admin\AdminProductController@user_products_delete')->name('user_products.delete');

        Route::get('products/status', 'Admin\AdminProductController@user_products_status')->name('user_products.status');

        Route::get('products/dashboard', 'Admin\AdminProductController@user_products_dashboard')->name('user_products.dashboard');

        Route::get('order_products','Admin\AdminProductController@order_products')->name('order_products');

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

        Route::get('posts' , 'Admin\AdminPostController@posts_index')->name('posts.index');

        Route::get('posts/delete', 'Admin\AdminPostController@posts_delete')->name('posts.delete');

        Route::get('posts/create', 'Admin\AdminPostController@posts_create')->name('posts.create');

        Route::get('posts/view', 'Admin\AdminPostController@posts_view')->name('posts.view');

        Route::post('posts/save', 'Admin\AdminPostController@posts_save')->name('posts.save');

        Route::get('posts/edit', 'Admin\AdminPostController@posts_edit')->name('posts.edit');

        Route::get('posts/dashboard', 'Admin\AdminPostController@posts_dashboard')->name('posts.dashboard');

        Route::get('posts/status', 'Admin\AdminPostController@posts_status')->name('posts.status');

        Route::get('posts/publish', 'Admin\AdminPostController@posts_publish')->name('posts.publish');

        Route::get('post/payments','Admin\AdminRevenueController@post_payments')->name('post.payments');

        Route::get('post/payments/view','Admin\AdminRevenueController@post_payments_view')->name('post.payments.view');

        Route::get('post_comments','Admin\AdminPostController@post_comments')->name('posts.comments');

        Route::get('post_comments/delete','Admin\AdminPostController@post_comment_delete')->name('post_comment.delete');



        //posts end

        //posts albums start

        Route::get('post_albums' , 'Admin\AdminPostController@post_albums_index')->name('post_albums.index');

        Route::get('post_albums/delete', 'Admin\AdminPostController@post_albums_delete')->name('post_albums.delete');

        Route::get('post_albums/view', 'Admin\AdminPostController@post_albums_view')->name('post_albums.view');

        Route::get('post_albums/status', 'Admin\AdminPostController@post_albums_status')->name('post_albums.status');

        //posts albums end

        //orders start

        Route::get('orders' , 'Admin\AdminPostController@orders_index')->name('orders.index');

        Route::get('orders/view', 'Admin\AdminPostController@orders_view')->name('orders.view');

        Route::get('order/payments','Admin\AdminRevenueController@order_payments')->name('order.payments');

        Route::get('order/payments/view','Admin\AdminRevenueController@order_payments_view')->name('order.payments.view');

        //orders end

        //delivery address routes start

        Route::get('delivery_address' , 'Admin\AdminPostController@delivery_address_index')->name('delivery_address.index');

        Route::get('delivery_address/delete', 'Admin\AdminPostController@delivery_address_delete')->name('delivery_address.delete');

        Route::get('delivery_address/view', 'Admin\AdminPostController@delivery_address_view')->name('delivery_address.view');

        //delivery address routes end

         //bookmarks routes start

        Route::get('bookmarks' , 'Admin\AdminPostController@post_bookmarks_index')->name('bookmarks.index');

        Route::get('bookmarks/delete', 'Admin\AdminPostController@post_bookmarks_delete')->name('bookmarks.delete');

        Route::get('bookmarks/view', 'Admin\AdminPostController@post_bookmarks_view')->name('bookmarks.view');
         //bookmarks routes start


        // fav users route start
        Route::get('fav_users','Admin\AdminPostController@fav_users')->name('fav_users.index');

        Route::get('fav_users/delete','Admin\AdminPostController@fav_users_delete')->name('fav_users.delete');

        // end of fav user route end


      // liked posts route start
        Route::get('post_likes','Admin\AdminPostController@post_likes')->name('post_likes.index');

        Route::get('post_likes/delete','Admin\AdminPostController@post_likes_delete')->name('post_likes.delete');

        // end of liked posts


        
        //user wallet route start

        Route::get('user_wallets' , 'Admin\AdminContentCreatorController@user_wallets_index')->name('user_wallets.index');

        Route::get('user_wallets/view', 'Admin\AdminContentCreatorController@user_wallets_view')->name('user_wallets.view');

        //user wallet route end

        //revenue dashboard start

        Route::get('revenues/dashboard','Admin\AdminRevenueController@revenues_dashboard')->name('revenues.dashboard');

        //revenue dashboard end

        //subscriptions start
        Route::get('subscriptions', 'Admin\AdminRevenueController@subscriptions_index')->name('subscriptions.index');

        Route::get('subscriptions/create', 'Admin\AdminRevenueController@subscriptions_create')->name('subscriptions.create');

        Route::get('subscriptions/edit', 'Admin\AdminRevenueController@subscriptions_edit')->name('subscriptions.edit');

        Route::post('subscriptions/save', 'Admin\AdminRevenueController@subscriptions_save')->name('subscriptions.save');
        

        Route::get('subscriptions/view', 'Admin\AdminRevenueController@subscriptions_view')->name('subscriptions.view');

        Route::get('subscriptions/delete', 'Admin\AdminRevenueController@subscriptions_delete')->name('subscriptions.delete');

        Route::get('subscriptions/status', 'Admin\AdminRevenueController@subscriptions_status')->name('subscriptions.status');

        Route::get('subscriptions_payments/index','Admin\AdminRevenueController@subscription_payments_index')->name('subscription_payments.index');

        Route::get('subscriptions_payments/view','Admin\AdminRevenueController@subscription_payments_view')->name('subscription_payments.view');
        //subscriptions end

        //categories start
        Route::get('categories', 'Admin\AdminProductController@categories_index')->name('categories.index');

        Route::get('categories/create', 'Admin\AdminProductController@categories_create')->name('categories.create');

        Route::get('categories/edit', 'Admin\AdminProductController@categories_edit')->name('categories.edit');

        Route::post('categories/save', 'Admin\AdminProductController@categories_save')->name('categories.save');

        Route::get('categories/view', 'Admin\AdminProductController@categories_view')->name('categories.view');

        Route::get('categories/delete', 'Admin\AdminProductController@categories_delete')->name('categories.delete');

        Route::get('categories/status', 'Admin\AdminProductController@categories_status')->name('categories.status');

        //categories end

        //sub_categories start
        Route::get('sub_categories', 'Admin\AdminProductController@sub_categories_index')->name('sub_categories.index');

        Route::get('sub_categories/create', 'Admin\AdminProductController@sub_categories_create')->name('sub_categories.create');

        Route::get('sub_categories/edit', 'Admin\AdminProductController@sub_categories_edit')->name('sub_categories.edit');

        Route::post('sub_categories/save', 'Admin\AdminProductController@sub_categories_save')->name('sub_categories.save');

        Route::get('sub_categories/view', 'Admin\AdminProductController@sub_categories_view')->name('sub_categories.view');

        Route::get('sub_categories/delete', 'Admin\AdminProductController@sub_categories_delete')->name('sub_categories.delete');

        Route::get('sub_categories/status', 'Admin\AdminProductController@sub_categories_status')->name('sub_categories.status');

        //sub_categories end

        // CC withdrawals start

        Route::get('user_withdrawals','Admin\AdminRevenueController@user_withdrawals')->name('user_withdrawals');

        Route::get('user_withdrawals/paynow','Admin\AdminRevenueController@user_withdrawals_paynow')->name('user_withdrawals.paynow');

        Route::get('user_withdrawals/reject','Admin\AdminRevenueController@user_withdrawals_reject')->name('user_withdrawals.reject');

         Route::get('user_withdrawals/view','Admin\AdminRevenueController@user_withdrawals_view')->name('user_withdrawals.view');

        // CC withdrawals end

        //inventory route start

        Route::get('product_inventories/index' , 'Admin\AdminRevenueController@product_inventories_index')->name('product_inventories.index');

        Route::get('product_inventories/view', 'Admin\AdminUserController@product_inventories_view')->name('product_inventories.view');

        //inventory route end

        //faq CRUD
        Route::get('faqs', 'Admin\AdminLookupController@faqs_index')->name('faqs.index');

        Route::get('faqs/create', 'Admin\AdminLookupController@faqs_create')->name('faqs.create');

        Route::get('faqs/edit', 'Admin\AdminLookupController@faqs_edit')->name('faqs.edit');

        Route::post('faqs/save', 'Admin\AdminLookupController@faqs_save')->name('faqs.save');

        Route::get('faqs/view', 'Admin\AdminLookupController@faqs_view')->name('faqs.view');

        Route::get('faqs/delete', 'Admin\AdminLookupController@faqs_delete')->name('faqs.delete');

        Route::get('faqs/status', 'Admin\AdminLookupController@faqs_status')->name('faqs.status');
        //faq end


        // Static pages start

        Route::get('static_pages' , 'Admin\AdminLookupController@static_pages_index')->name('static_pages.index');

        Route::get('static_pages/create', 'Admin\AdminLookupController@static_pages_create')->name('static_pages.create');

        Route::get('static_pages/edit', 'Admin\AdminLookupController@static_pages_edit')->name('static_pages.edit');

        Route::post('static_pages/save', 'Admin\AdminLookupController@static_pages_save')->name('static_pages.save');

        Route::get('static_pages/delete', 'Admin\AdminLookupController@static_pages_delete')->name('static_pages.delete');

        Route::get('static_pages/view', 'Admin\AdminLookupController@static_pages_view')->name('static_pages.view');

        Route::get('static_pages/status', 'Admin\AdminLookupController@static_pages_status_change')->name('static_pages.status');

        // Static pages end

        // settings

        Route::get('admin-control', 'Admin\AdminSettingController@admin_control')->name('control');

        Route::get('settings', 'Admin\AdminSettingController@settings')->name('settings'); 

        Route::post('settings/save', 'Admin\AdminSettingController@settings_save')->name('settings.save'); 

        Route::post('env_settings','Admin\AdminSettingController@env_settings_save')->name('env-settings.save');

        Route::get('support_tickets/index','Admin\AdminSupportMemberController@support_tickets_index')->name('support_tickets.index');

        Route::get('support_tickets/view','Admin\AdminSupportMemberController@support_tickets_view')->name('support_tickets.view');

        Route::get('support_tickets/create', 'Admin\AdminSupportMemberController@support_tickets_create')->name('support_tickets.create');

        Route::post('support_tickets/save', 'Admin\AdminSupportMemberController@support_tickets_save')->name('support_tickets.save');
        
        Route::get('support_tickets/edit', 'Admin\AdminSupportMemberController@support_tickets_edit')->name('support_tickets.edit');

        Route::get('support_tickets/delete', 'Admin\AdminSupportMemberController@support_tickets_delete')->name('support_tickets.delete');


        //followers
         Route::get('followers' , 'Admin\AdminContentCreatorController@users_followers')->name('users.followers');

        Route::get('followings' , 'Admin\AdminContentCreatorController@users_followings')->name('users.followings');

        // Support Members Operations

        Route::get('support_members/index', 'Admin\AdminSupportMemberController@support_members_index')->name('support_members.index');  

        Route::get('support_members/create', 'Admin\AdminSupportMemberController@support_members_create')->name('support_members.create');

        Route::get('support_members/view', 'Admin\AdminSupportMemberController@support_members_view')->name('support_members.view');

        Route::post('support_members/save', 'Admin\AdminSupportMemberController@support_members_save')->name('support_members.save');

        Route::get('support_members/edit', 'Admin\AdminSupportMemberController@support_members_edit')->name('support_members.edit');

        Route::get('support_members/delete', 'Admin\AdminSupportMemberController@support_members_delete')->name('support_members.delete');

        Route::get('support_members/status', 'Admin\AdminSupportMemberController@support_members_status')->name('support_members.status');

        Route::get('support_members/verify', 'Admin\AdminSupportMemberController@support_members_verify_status')->name('support_members.verify');


     });
});