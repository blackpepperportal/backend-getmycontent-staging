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
}