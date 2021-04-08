<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use Carbon\Carbon;

class CommonRepository {

	/**
     *
     * @method user_premium_account_check()
     *
     * @uses premium account user 
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param 
     *
     * @return
     */
    public static function user_premium_account_check($user) {

        try {

            if($user->is_email_verified == USER_EMAIL_NOT_VERIFIED) {

                throw new Exception(api_error(157), 157);
                
            }

            if($user->is_document_verified != USER_DOCUMENT_APPROVED) {

                $code = $user->userDocuments->count() ? 158 : 160;

                if($user->is_document_verified == USER_DOCUMENT_DECLINED) {

                    $code = 159;

                }

                throw new Exception(api_error($code), $code);
                
            }

            $check_billing_accounts = $user->userBillingAccounts->where('user_billing_accounts.is_default', YES)->first();

            if($check_billing_accounts) {

                throw new Exception(api_error(161), 161);
            }

            $response = ['success' => true, 'message' => api_success('')];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     *
     * @method follow_user()
     *
     * @uses Follow the user
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param 
     *
     * @return
     */
    
    public static function follow_user($request, $user = []) {

        try {

            DB::beginTransaction();
            
            // Validation start
            // Follower id
            $rules = [
                'user_id' => 'required|exists:users,id'
            ];

            $custom_errors = ['user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            // Validation end
            if($request->id == $request->user_id) {

                throw new Exception(api_error(136), 136);

            }

            $follow_user = \App\User::where('id', $request->user_id)->first();

            if(!$follow_user) {

                throw new Exception(api_error(135), 135);
            }


            // Check the user already following the selected users
            $follower = \App\Follower::where('status', YES)->where('follower_id', $request->id)->where('user_id', $request->user_id)->first();

            if($follower) {

                throw new Exception(api_error(137), 137);

            }

            $follower = \App\Follower::where('follower_id', $request->id)->where('user_id', $request->user_id)->first() ?? new \App\Follower;

            $follower->user_id = $request->user_id;

            $follower->follower_id = $request->id;

            $follower->status = DEFAULT_TRUE;

            $follower->save();

            DB::commit();

            $job_data['follower'] = $follower;

            $job_data['timezone'] = $request->timezone ?? '';

            dispatch(new \App\Jobs\FollowUserJob($job_data));

            $data['user_id'] = $request->user_id;

            $data['is_follow'] = NO;

            $response = ['success' => true, 'message' => api_success(128,$follow_user->username ?? 'user'), 'code' => 128, 'data' => $data];

            Log::info("Follow User".print_r($data, true));

            return (object) $response;

        } catch(Exception $e) {

            DB::rollback();

            Log::info("error message".print_r($e->getMessage(), true));

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return (object) $response;
        
        }

    }

    /**
     * @method subscriptions_user_payment_check()
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

    public static function subscriptions_user_payment_check($other_user, $request) {

        $data['is_user_needs_pay'] = $data['is_free_account'] = NO;

        $data['payment_text'] = "";

        $data['unsubscribe_btn_status'] = NO;

        $login_user = \App\User::find($request->id);

        // Check the user already following
        $follower = \App\Follower::where('status', YES)->where('follower_id', $request->id)->where('user_id', $other_user->user_id)->first();

        if(!$follower) {

            $data['is_user_needs_pay'] = YES;

            $data['is_free_account'] =  NO;

            $data['payment_text'] = tr('subscribe_for_free');
 
        } else {

            $data['unsubscribe_btn_status'] = YES;
        }

        // Check the user has subscribed for this user plans

        $user_subscription = \App\UserSubscription::where('user_id', $other_user->id)->first();

        $data['subscription_info'] = [];

        if($user_subscription) {

            $data['subscription_info'] = $user_subscription;

            if($user_subscription->monthly_amount <= 0 && $user_subscription->yearly_amount <= 0) {

            } else {

                $current_date = Carbon::now()->format('Y-m-d');

                $check_user_subscription_payment = \App\UserSubscriptionPayment::where('user_subscription_id', $user_subscription->id)->where('from_user_id', $request->id)
                    ->where('is_current_subscription',YES)
                    ->whereDate('expiry_date','>=',$current_date)
                    ->where('to_user_id', $other_user->id)->count();
                
                if(!$check_user_subscription_payment) {

                    $data['is_user_needs_pay'] = YES;

                    $data['payment_text'] = tr('unlock_subscription_text', $user_subscription->monthly_amount_formatted);

                    $data['unsubscribe_btn_status'] = NO;

                }
            
            }

        } else {
            
            $data['is_free_account'] = YES;
        }

        return (object)$data;
    
    }

    /**
     * @method followings_list_response()
     *
     * @uses Format the follow user response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function followers_list_response($followers, $request) {
        
        $followers = $followers->map(function ($follower, $key) use ($request) {

                        $other_user = \App\User::OtherResponse()->find($follower->follower_id) ?? new \stdClass; 

                        $other_user->is_block_user = Helper::is_block_user($request->id, $follower->follower_id);

                        $other_user->is_owner = $request->id == $follower->follower_id ? YES : NO;

                        $is_you_following = Helper::is_you_following($request->id, $follower->follower_id);

                        $other_user->show_follow = $is_you_following ? HIDE : SHOW;

                        $other_user->show_unfollow = $is_you_following ? SHOW : HIDE;

                        $other_user->is_fav_user = Helper::is_fav_user($request->id, $follower->follower_id);

                        $follower->otherUser = $other_user ?? [];

                        return $follower;
                    });


        return $followers;

    }

    /**
     * @method followings_list_response()
     *
     * @uses Format the follow user response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function followings_list_response($followers, $request) {
        
        $followers = $followers->map(function ($follower, $key) use ($request) {

                        $other_user = \App\User::OtherResponse()->find($follower->user_id) ?? new \stdClass; 

                        $other_user->is_block_user = Helper::is_block_user($request->id, $follower->user_id);

                        $other_user->is_owner = $request->id == $follower->user_id ? YES : NO;

                        $is_you_following = Helper::is_you_following($request->id, $follower->user_id);

                        $other_user->show_follow = $is_you_following ? HIDE : SHOW;

                        $other_user->show_unfollow = $is_you_following ? SHOW : HIDE;

                        $other_user->is_fav_user = Helper::is_fav_user($request->id, $follower->user_id);

                        $follower->otherUser = $other_user ?? [];

                        return $follower;
                    });


        return $followers;

    }

    /**
     * @method followings_list_response()
     *
     * @uses Format the follow user response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function favorites_list_response($fav_users, $request) {
        
         $fav_users = $fav_users->map(function ($data, $key) use ($request) {

                $fav_user = \App\User::OtherResponse()->find($data->fav_user_id) ?? new \stdClass; 

                $fav_user->is_fav_user = Helper::is_fav_user($request->id, $data->fav_user_id);

                $fav_user->is_block_user = Helper::is_block_user($request->id, $data->fav_user_id);

                $fav_user->is_owner = $request->id == $data->fav_user_id ? YES : NO;

                $is_you_following = Helper::is_you_following($request->id, $data->fav_user_id);

                $fav_user->show_follow = $is_you_following ? HIDE : SHOW;

                $fav_user->show_unfollow = $is_you_following ? SHOW : HIDE;

                $data->fav_user = $fav_user ?? [];

                return $data;
        });



        return $fav_users;

    }

    /**
     * @method chat_user_update()
     *
     * @uses 
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param boolean
     *
     * @return boolean response
     */
    public static function chat_user_update($from_user_id,$to_user_id) {

        try {

            DB::beginTransaction();

            $chat_user = \App\ChatUser::where('from_user_id', $from_user_id)->where('to_user_id', $to_user_id)->first() ?? new \App\ChatUser();

            $chat_user->from_user_id = $from_user_id;

            $chat_user->to_user_id = $to_user_id;

            $chat_user->status = $chat_user->status ? NO : YES;
            
            $chat_user->save();
            
            DB::commit();

        } catch(Exception $e) {

            DB::rollback();

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }
}