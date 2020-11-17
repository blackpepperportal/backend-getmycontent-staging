<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\User;

class PostRepository {

    /**
     * @method posts_list_response()
     *
     * @uses Format the post response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function posts_list_response($posts, $request) {
        
        $posts = $posts->map(function ($post, $key) use ($request) {

                        $post->is_user_needs_pay = $post->is_paid_post;

                        $post->is_user_subscribed = NO;

                        $post->is_ppv = NO;

                        $post->is_user_liked = $post->postLikes->where('user_id', $request->id)->count() ? YES : NO;

                        $post->is_user_bookmarked = $post->postBookmarks->where('user_id', $request->id)->count() ? YES : NO;

                        $post->share_link = Setting::get('frontend_url')."/post/".$post->post_unique_id;

                        $post->payment_info = self::posts_user_payment_check($post, $request);

                        $is_user_needs_pay = $post->payment_info->data->is_user_needs_pay ?? NO; 

                        $post->post_files = \App\PostFile::where('post_id', $post->post_id)->when($is_user_needs_pay == NO, function ($q) use ($is_user_needs_pay) {
                                                    return $q->OriginalResponse();
                                                })
                                                ->when($is_user_needs_pay == YES, function($q) use ($is_user_needs_pay) {
                                                    return $q->BlurResponse();
                                                })->get();

                        $post->publish_time_formatted = common_date($post->publish_time, $request->timezone, 'M d');

                        $post->unsetRelation('postLikes')->unsetRelation('postBookmarks')->unsetRelation('user');


                        return $post;
                    });


        return $posts;

    }
    
    /**
     * @method posts_single_response()
     *
     * @uses Format the post response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function posts_single_response($post, $request) {
        
        $post->is_user_needs_pay = $post->is_paid_post;

        $post->is_user_subscribed = NO;

        $post->is_ppv = NO;

        $post->is_user_liked = $post->postLikes->where('user_id', $request->id)->count() ? YES : NO;

        $post->is_user_bookmarked = $post->postBookmarks->where('user_id', $request->id)->count() ? YES : NO;

        $post->share_link = Setting::get('frontend_url')."/post/".$post->post_unique_id;

        $post->unsetRelation('postLikes')->unsetRelation('postBookmarks')->unsetRelation('user');

        return $post;
    
    }

    /**
     * @method posts_user_payment_check()
     *
     * @uses Check the post payment status for each post
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function posts_user_payment_check($post, $request) {

        $post_user = $post->user ?? [];

        $data['is_user_needs_pay'] = NO;

        $data['post_payment_type'] = $data['payment_text'] = "";

        if(!$post_user) {

            goto post_end;

        }

        $post_user_account_type = $post_user->user_account_type ?? USER_FREE_ACCOUNT;

        $login_user = \App\User::find($request->id);

        $post_payment_check = NO;

        if($post_user_account_type == USER_FREE_ACCOUNT) {

            if($post->is_paid_post && $post->amount > 0) {

                $post_payment_check = YES;

                goto post_payment_check;

            }

        } elseif ($post_user_account_type == USER_PREMIUM_ACCOUNT) {

            // Check the post paid or normal post

            if($post->is_paid_post && $post->amount > 0) {

                $post_payment_check = YES;

                goto post_payment_check;

            }

            // Check the user has subscribed for this post user plans

            $user_subscription = \App\UserSubscription::where('user_id', $post_user->id)->first();

            if($user_subscription) {

                if($user_subscription->monthly_amount <= 0 && $user_subscription->yearly_amount <= 0) {

                } else {

                    $check_user_subscription_payment = \App\UserSubscriptionPayment::where('user_subscription_id', $user_subscription->id)->where('from_user_id', $request->id)->count();

                    if(!$check_user_subscription_payment) {

                        $data['is_user_needs_pay'] = YES;

                        $data['post_payment_type'] = POSTS_PAYMENT_SUBSCRIPTION;

                        $data['payment_text'] = tr('unlock_subscription_text', $user_subscription->monthly_amount_formatted);

                    }
                }

            }

        } else {

        }

        post_payment_check:

        if($post_payment_check == YES) {

            // Check the user already paid

            $post_payment = \App\PostPayment::where('user_id', $request->id)->where('post_id', $post->post_id)->where('status', PAID)->count();

            if(!$post_payment) {

                $data['is_user_needs_pay'] = YES;

                $data['post_payment_type'] = POSTS_PAYMENT_PPV;

                $data['payment_text'] = tr('unlock_post_text', $post->amount_formatted);
            }
        
        }

        post_end:

        return (object)$data;
    
    }
}