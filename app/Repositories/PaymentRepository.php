<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\User, App\SubscriptionPayment;

class PaymentRepository {

    /**
     * @method user_wallets_payment_save()
     *
     * @uses used to save user wallet payment details
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $user_wallet_payment
     */

    public static function user_wallets_payment_save($request) {

        try {

            $user_wallet_payment = new \App\UserWalletPayment;
            
            $user_wallet_payment->user_id = $request->id;

            $user_wallet_payment->to_user_id = $request->to_user_id ?? 0;

            $user_wallet_payment->received_from_user_id = $request->received_from_user_id ?? 0;

            $user_wallet_payment->user_billing_account_id = $request->user_billing_account_id ?: 0;
            
            $user_wallet_payment->payment_id = $request->payment_id ?:generate_payment_id();

            $user_wallet_payment->paid_amount = $user_wallet_payment->requested_amount = $request->paid_amount ?? 0.00;

            $user_wallet_payment->admin_amount = $request->admin_amount ?? 0.00;

            $user_wallet_payment->user_amount = $request->user_amount ?? 0.00;

            $user_wallet_payment->payment_type = $request->payment_type ?: WALLET_PAYMENT_TYPE_ADD;

            $user_wallet_payment->amount_type = $request->amount_type ?: WALLET_AMOUNT_TYPE_ADD;

            $user_wallet_payment->usage_type = $request->usage_type ?: "";

            $user_wallet_payment->currency = Setting::get('currency') ?? "$";

            $user_wallet_payment->payment_mode = $request->payment_mode ?? CARD;

            $user_wallet_payment->paid_date = date('Y-m-d H:i:s');

            $user_wallet_payment->status = $request->paid_status ?: USER_WALLET_PAYMENT_PAID;

            if($request->file('bank_statement_picture')) {

                $user_wallet_payment->bank_statement_picture = Helper::storage_upload_file($request->file('bank_statement_picture'));

            }

            $user_wallet_payment->message = "";

            $user_wallet_payment->save();

            $message = strtoupper($request->usage_type)." - " ?: "";

            $message .= get_wallet_message($user_wallet_payment);

            $message .= $request->message ? " - ".$request->message : "";

            $user_wallet_payment->message = $message;

            $user_wallet_payment->save();

            if($user_wallet_payment->payment_type != WALLET_PAYMENT_TYPE_WITHDRAWAL && $user_wallet_payment->status == USER_WALLET_PAYMENT_PAID) {

                self::user_wallet_update($user_wallet_payment);
            }

            $response = ['success' => true, 'message' => 'paid', 'data' => $user_wallet_payment];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method user_wallets_payment_by_stripe()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallets_payment_by_stripe($request) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: 'USD';

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //                     'amount' => $total,
            //                     'currency' => $currency_code,
            //                     'customer' => $request->customer_id,
            //                 ];


            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,

                                'paid_status' => $stripe_payment_response->paid ?? true
                            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update($user_wallet_payment) {

        try {

            $user_wallet = \App\UserWallet::where('user_id', $user_wallet_payment->user_id)->first() ?: new \App\UserWallet;

            $user_wallet->user_id = $user_wallet_payment->user_id;

            if($user_wallet_payment->amount_type == WALLET_AMOUNT_TYPE_ADD) {

                $user_wallet->total += $user_wallet_payment->user_amount;

                $user_wallet->remaining += $user_wallet_payment->user_amount;

            } else {

                $user_wallet->used += $user_wallet_payment->user_amount;

                $user_wallet->remaining -= $user_wallet_payment->user_amount;
            }

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_withdraw_send()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_withdraw_send($amount, $user_id) {
        
        try {

            $user_wallet = \App\UserWallet::where('user_id', $user_id)->first() ?: new \App\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining -= $amount;

            $user_wallet->onhold += $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_withdraw_cancel()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_withdraw_cancel($amount, $user_id) {

        try {

            $user_wallet = \App\UserWallet::where('user_id', $user_id)->first() ?: new \App\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining += $amount;

            $user_wallet->onhold -= $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_withdraw_paynow()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_withdraw_paynow($amount, $user_id) {

        try {

            $user_wallet = \App\UserWallet::where('user_id', $user_id)->first() ?: new \App\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->onhold -= $amount;

            $user_wallet->used += $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_dispute_send()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_dispute_send($amount, $user_id) {

        try {

            $user_wallet = \App\UserWallet::where('user_id', $user_id)->first() ?: new \App\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining -= $amount;

            $user_wallet->onhold += $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_dispute_cancel()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_dispute_cancel($amount, $user_id) {

        try {

            $user_wallet = \App\UserWallet::where('user_id', $user_id)->first() ?: new \App\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining += $amount;

            $user_wallet->onhold -= $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_dispute_approve()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_dispute_approve($amount, $user_id, $receiver_user_id) {

        try {

            // Winner wallet update

            $user_wallet = \App\UserWallet::where('user_id', $user_id)->first() ?: new \App\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining += $amount;

            $user_wallet->used -= $amount;

            $user_wallet->save();

            // Loser wallet update
            $receiver_user_wallet = \App\UserWallet::where('user_id', $receiver_user_id)->first() ?: new \App\UserWallet;

            $receiver_user_wallet->user_id = $receiver_user_id;

            $receiver_user_wallet->total -= $amount;

            $receiver_user_wallet->onhold -= $amount;

            $receiver_user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_dispute_reject()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_dispute_reject($amount, $receiver_user_id) {

        try {

            // Opposite party wallet update
            $receiver_user_wallet = \App\UserWallet::where('user_id', $receiver_user_id)->first() ?: new \App\UserWallet;

            $receiver_user_wallet->user_id = $receiver_user_id;

            $receiver_user_wallet->total += $remaining;

            $receiver_user_wallet->onhold -= $amount;

            $receiver_user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_invoice_payment()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_invoice_payment($amount, $sender_id, $to_user_id) {

        try {

            // Receiver wallet update

            $sender_wallet = \App\UserWallet::where('user_id', $sender_id)->first() ?: new \App\UserWallet;

            Log::info("sender_wallet".print_r($sender_wallet->toArray(), true));

            $sender_wallet->user_id = $sender_id;

            $sender_wallet->total += $amount;

            $sender_wallet->remaining += $amount;

            $sender_wallet->save();

            // Payer wallet update
            $to_user_wallet = \App\UserWallet::where('user_id', $to_user_id)->first() ?: new \App\UserWallet;

            Log::info("to_user_wallet".print_r($to_user_wallet->toArray(), true));

            $to_user_wallet->user_id = $to_user_id;

            $to_user_wallet->remaining -= $amount;

            $to_user_wallet->used += $amount;

            $to_user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $sender_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            Log::info("error".print_r($e->getMessage(), true));

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method subscriptions_payment_by_stripe()
     *
     * @uses Subscription payment - card
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param object $subscription, object $request
     *
     * @return object $subscription
     */

    public static function subscriptions_payment_by_stripe($request, $subscription) {

        try {

            // Check stripe configuration

            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];

            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method subscriptions_payment_save()
     *
     * @uses used to save user subscription payment details
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param object $subscription, object $request
     *
     * @return object $subscription
     */

    public static function subscriptions_payment_save($request, $subscription) {

        try {

            $previous_payment = SubscriptionPayment::where('user_id' , $request->id)
                ->where('status', PAID_STATUS)
                ->orderBy('created_at', 'desc')
                ->first();

            $user_subscription = new SubscriptionPayment;

            $user_subscription->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription->plan} months"));

            if($previous_payment) {

                if (strtotime($previous_payment->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {
                    $user_subscription->expiry_date = date('Y-m-d H:i:s', strtotime("+{$subscription->plan} months", strtotime($previous_payment->expiry_date)));
                }
            }

            $user_subscription->subscription_id = $request->subscription_id;

            $user_subscription->user_id = $request->id;

            $user_subscription->payment_id = $request->payment_id ?? "NO-".rand();

            $user_subscription->status = PAID_STATUS;

            $user_subscription->amount = $request->paid_amount ?? 0.00;

            $user_subscription->payment_mode = $request->payment_mode ?? CARD;

            $user_subscription->cancel_reason = $request->cancel_reason ?? '';

            $user_subscription->save();

            // update the earnings
            self::users_account_upgrade($request->id, $request->paid_amount, $subscription->amount, $user_subscription->expiry_date);

            $response = ['success' => true, 'message' => 'paid', 'data' => ['user_type' => SUBSCRIBED_USER, 'payment_id' => $request->payment_id]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method users_account_upgrade()
     *
     * @uses add amount to user
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param integer $user_id, float $admin_amount, $user_amount
     *
     * @return - 
     */
    
    public static function users_account_upgrade($user_id, $paid_amount = 0.00, $subscription_amount, $expiry_date) {

        if($user = User::find($user_id)) {

            $user->user_type = SUBSCRIBED_USER;

            $user->one_time_subscription = $subscription_amount <= 0 ? YES : NO;

            $user->amount_paid += $paid_amount ?? 0.00;

            $user->expiry_date = $expiry_date;

            $user->no_of_days = total_days($expiry_date);

            $user->save();
        
        }
    
    }

    /**
     * @method posts_payment_by_stripe()
     *
     * @uses post payment - card
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param object $post, object $request
     *
     * @return object $post_paym
     */

    public static function posts_payment_by_stripe($request, $post) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];


            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method post_payments_save()
     *
     * @uses used to save post payment details
     *
     * @created Bhawya
     * 
     * @updated Vithya
     *
     * @param object $post, object $request
     *
     * @return object $post_payment
     */

    public static function post_payments_save($request, $post) {

        try {

            $post_payment = new \App\PostPayment;

            $post_payment->post_id = $request->post_id;

            $post_payment->user_id = $request->id;

            $post_payment->payment_id = $request->payment_id ?? "NO-".rand();

            $post_payment->payment_mode = $request->payment_mode ?? CARD;

            $post_payment->paid_amount = $total = $request->paid_amount ?? 0.00;

            // Commission calculation

            $admin_commission_in_per = Setting::get('admin_commission', 1)/100;

            $admin_amount = $total * $admin_commission_in_per;

            $user_amount = $total - $admin_amount;

            $post_payment->admin_amount = $admin_amount ?? 0.00;

            $post_payment->user_amount = $user_amount ?? 0.00;

            $post_payment->paid_date = date('Y-m-d H:i:s');

            $post_payment->status = PAID;

            $post_payment->save();

            // Add to post user wallet

            self::post_payment_wallet_update($request, $post, $post_payment);

            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'payment_id' => $request->payment_id, 'post' => $post]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method tips_payment_by_stripe()
     *
     * @uses tips payment - card
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param object $post, object $request
     *
     * @return object $post_paym
     */

    public static function tips_payment_by_stripe($request, $post) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];

            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method tips_payment_save()
     *
     * @uses used to save tips payment details
     *
     * @created Bhawya
     * 
     * @updated Vithya
     *
     * @param object $post, object $request
     *
     * @return object $post_payment
     */

    public static function tips_payment_save($request) {

        try {

            $user_tip = new \App\UserTip;

            $user_tip->post_id = $request->post_id ?: 0;

            $user_tip->user_id = $request->id;

            $user_tip->to_user_id = $request->to_user_id;

            $user_tip->user_card_id = $request->user_card_id ?: 0;

            $user_tip->payment_id = $request->payment_id ?? "NO-".rand();

            $user_tip->payment_mode = $request->payment_mode ?? CARD;

            $user_tip->amount = $total = $request->paid_amount ?? 0.00;

            $user_tip->message = $request->message ?: "";

            $user_tip->user_wallet_payment_id = $request->user_wallet_payment_id ?? 0;

            // Commission calculation

            $tips_admin_commission_in_per = Setting::get('tips_admin_commission', 1)/100;

            $tips_admin_amount = $total * $tips_admin_commission_in_per;

            $user_amount = $total - $tips_admin_amount;

            $user_tip->admin_amount = $tips_admin_amount ?? 0.00;
 
            $user_tip->user_amount = $user_amount ?? 0.00;

            $user_tip->paid_date = date('Y-m-d H:i:s');

            $user_tip->status = PAID;

            $user_tip->save();

            // Add to post user wallet

            self::tips_payment_wallet_update($request, $user_tip);

            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'payment_id' => $request->payment_id]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method post_payment_wallet_update
     *
     * @uses post payment amount will update to the post owner wallet
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return
     */

    public static function post_payment_wallet_update($request, $post, $post_payment) {

        try {

            $to_user_inputs = [
                'id' => $post->user_id,
                'received_from_user_id' => $request->id,
                'total' => $post_payment->paid_amount, 
                'user_pay_amount' => $post_payment->paid_amount,
                'paid_amount' => $post_payment->paid_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $post_payment->payment_id,
                'admin_amount' => $post_payment->admin_amount,
                'user_amount' => $post_payment->user_amount,
                'usage_type' => USAGE_TYPE_PPV
            ];

            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /** | | | DONT GET CONFUSE WITH ADMIN SUBSCRIPTION. THIS FUNCTIONS ARE USED FOR OTHER USER SUBSCRIPTION PAYMENTs */

    /**
     * @method user_subscription_payments_save()
     *
     * @uses save the payment details when logged in user subscribe the other user plans
     *
     * @created Vithya
     * 
     * @updated Vithya
     *
     * @param object $user_subscription, object $request
     *
     * @return object $user_subscription
     */

    public static function user_subscription_payments_save($request, $user_subscription) {

        try {

            $user = \App\User::where('users.unique_id', $request->user_unique_id)->first();

            $previous_payment = \App\UserSubscriptionPayment::where('from_user_id', $request->id)->where('to_user_id', $user_subscription->user_id)->where('is_current_subscription', YES)->first();

            $user_subscription_payment = new \App\UserSubscriptionPayment;

            $plan = 1;

            $plan_type = $request->plan_type == PLAN_TYPE_YEAR ? 'years' : 'months';

            $plan_formatted = $plan." ".$plan_type;

            $user_subscription_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$plan_formatted}"));

            if($previous_payment) {

                if (strtotime($previous_payment->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {
                    $user_subscription_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$plan_formatted}", strtotime($previous_payment->expiry_date)));
                }
            }

            $user_subscription_payment->user_subscription_id = $user_subscription->id ?? 0;

            $user_subscription_payment->from_user_id = $request->id;

            $user_subscription_payment->to_user_id = $user->id;

            $user_subscription_payment->payment_id = $request->payment_id ?? "NO-".rand();

            $user_subscription_payment->status = $request->payment_status ?? PAID_STATUS;

            $user_subscription_payment->is_current_subscription = YES;

            $user_subscription_payment->amount = $total = $request->paid_amount ?? 0.00;

            $user_subscription_payment->payment_mode = $request->payment_mode ?? CARD;

            $user_subscription_payment->paid_date = now();

            $user_subscription_payment->plan = 1;

            $user_subscription_payment->plan_type = $request->plan_type ?: PLAN_TYPE_MONTH;

            $user_subscription_payment->cancel_reason = $request->cancel_reason ?? '';

            $user_subscription_payment->trans_token = $request->trans_token ?? '';
            // Commission calculation & update the earnings to other user wallet

            $admin_commission_in_per = Setting::get('subscription_admin_commission', 1)/100;

            $admin_amount = $total * $admin_commission_in_per;

            $user_amount = $total - $admin_amount;

            $user_subscription_payment->admin_amount = $admin_amount ?? 0.00;

            $user_subscription_payment->user_amount = $user_amount ?? 0.00;

            $user_subscription_payment->status = $request->payment_status ?? PAID;

            $user_subscription_payment->save();
            
            // Add to post user wallet
            if($user_subscription_payment->status == PAID_STATUS) {

                if($total > 0) {
                    self::user_subscription_payments_wallet_update($request, $user_subscription, $user_subscription_payment);
                }

                $request->request->add(['user_id' => $user->id]);

                \App\Repositories\CommonRepository::follow_user($request);

            }
            
            $data = ['user_type' => SUBSCRIBED_USER, 'payment_id' => $request->payment_id ?? $user_subscription_payment->payment_id];

            $data['total_followers'] = \App\Follower::where('user_id', $request->id)->where('status', YES)->count();

            $data['total_followings'] = \App\Follower::where('follower_id', $request->id)->where('status', YES)->count();

            $response = ['success' => true, 'message' => 'paid', 'data' => $data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method user_subscriptions_payment_by_stripe()
     *
     * @uses deduct the subscription amount when logged in user subscribe the other user plans
     *
     * @created vithya
     * 
     * @updated vithya
     *
     * @param object $user_subscription, object $request
     *
     * @return object $user_subscription
     */

    public static function user_subscriptions_payment_by_stripe($request, $user_subscription) {

        try {

            // Check stripe configuration

            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];


            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_subscription_payment_wallet_update
     *
     * @uses post payment amount will update to the post owner wallet
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return
     */

    public static function user_subscription_payments_wallet_update($request, $user_subscription, $user_subscription_payment) {

        try {

            $to_user_inputs = [
                'id' => $user_subscription_payment->to_user_id,
                'payment_mode' => $user_subscription_payment->payment_mode,
                'received_from_user_id' => $user_subscription_payment->from_user_id,
                'total' => $user_subscription_payment->amount, 
                'user_pay_amount' => $user_subscription_payment->amount,
                'paid_amount' => $user_subscription_payment->amount,
                'user_amount' => $user_subscription_payment->user_amount,
                'admin_amount' => $user_subscription_payment->admin_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $user_subscription_payment->payment_id,
                'usage_type' => USAGE_TYPE_SUBSCRIPTION
            ];


            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method tips_payment_wallet_update
     *
     * @uses tip payment amount will update to the post owner wallet
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return
     */

    public static function tips_payment_wallet_update($request, $user_tip) {

        try {

            $to_user_inputs = [
                'id' => $request->to_user_id,
                'received_from_user_id' => $request->id,
                'total' => $user_tip->amount, 
                'user_pay_amount' => $user_tip->amount,
                'paid_amount' => $user_tip->amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $user_tip->payment_id,
                'user_amount' => $user_tip->user_amount,
                'admin_amount' => $user_tip->admin_amount,
                'usage_type' => USAGE_TYPE_TIP,
                'message' => $request->message
            ];

            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method chat_assets_payment_by_stripe()
     *
     * @uses 
     *
     * @created Arun
     * 
     * @updated Arun
     *
     * @param object $chat_message, object $request
     *
     * @return object $chat_message
     */

    public static function chat_assets_payment_by_stripe($request, $chat_message) {

        try {

            // Check stripe configuration

            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method chat_assets_payment_save()
     *
     * @uses used to save chat_assets payment details
     *
     * @created Arun
     * 
     * @updated Arun
     *
     * @param object $request
     *
     * @return object $chat_asset_payment
     */

    public static function chat_assets_payment_save($request, $chat_message) {

        try {

            $chat_asset_payment = new \App\ChatAssetPayment;
            
            $chat_asset_payment->from_user_id = $chat_message->from_user_id;

            $chat_asset_payment->to_user_id = $chat_message->to_user_id;

            $chat_asset_payment->chat_message_id = $chat_message->chat_message_id;

            $chat_asset_payment->user_card_id = $request->user_card_id ?: 0;
            
            $chat_asset_payment->payment_id = $request->payment_id ?:generate_payment_id();

            $chat_asset_payment->paid_amount = $request->paid_amount ?? 0.00;

            $chat_asset_payment->currency = Setting::get('currency') ?? "$";

            $chat_asset_payment->payment_mode = $request->payment_mode ?? CARD;

            $chat_asset_payment->paid_date = date('Y-m-d H:i:s');

            $chat_asset_payment->status = $request->paid_status ?: PAID;

            $commission = admin_commission_spilit($request->paid_amount);

            $chat_asset_payment->admin_amount = $commission->admin_amount ?? 0.00;

            $chat_asset_payment->user_amount = $commission->user_amount ?? 0.00;

            $chat_asset_payment->save();

            $response = ['success' => true, 'message' => 'paid', 'data' => $chat_asset_payment];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

}