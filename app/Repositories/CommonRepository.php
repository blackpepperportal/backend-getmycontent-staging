<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

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

            $follow_user = User::where('id', $request->user_id)->first();

            if(!$follow_user) {

                throw new Exception(api_error(135), 135);
            }


            // Check the user already following the selected users
            $follower = \App\Follower::where('status', YES)->where('follower_id', $request->id)->where('user_id', $request->user_id)->first();

            if($follower) {

                throw new Exception(api_error(137), 137);

            }

            $follower = new \App\Follower;

            $follower->user_id = $request->user_id;

            $follower->follower_id = $request->id;

            $follower->status = DEFAULT_TRUE;

            $follower->save();

            DB::commit();

            $job_data['follower'] = $follower;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\FollowUserJob($job_data));

            $data['user_id'] = $request->user_id;

            $data['is_follow'] = NO;

            $response = ['success' => true, 'message' => api_success(128,$follow_user->username ?? 'user'), 'code' => 128, 'data' => $data];

            return (object) $response;

        } catch(Exception $e) {

            DB::rollback();

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

        $data['is_user_needs_pay'] = NO;

        $data['payment_text'] = "";

        $login_user = \App\User::find($request->id);

        // Check the user already following
        $follower = \App\Follower::where('status', YES)->where('follower_id', $request->id)->where('user_id', $other_user->user_id)->first();

        if(!$follower) {

            $data['payment_text'] = tr('subscribe_for_free');

        }

        // Check the user has subscribed for this user plans

        $user_subscription = \App\UserSubscription::where('user_id', $other_user->id)->first();

        if($user_subscription) {

            $data['subscription_info'] = $user_subscription;

            if($user_subscription->monthly_amount <= 0 && $user_subscription->yearly_amount <= 0) {

            } else {

                $check_user_subscription_payment = \App\UserSubscriptionPayment::where('user_subscription_id', $user_subscription->id)->where('from_user_id', $request->id)->count();

                if(!$check_user_subscription_payment) {

                    $data['is_user_needs_pay'] = YES;

                    $data['payment_text'] = tr('unlock_subscription_text', $user_subscription->monthly_amount_formatted);

                }
            }

        }

        return (object)$data;
    
    }
}