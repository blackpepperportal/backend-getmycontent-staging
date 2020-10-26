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
     * @return object $user_wallet_payment_details
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

            $user_wallet_payment->payment_type = $request->payment_type ?: WALLET_PAYMENT_TYPE_ADD;

            $user_wallet_payment->amount_type = $request->amount_type ?: WALLET_AMOUNT_TYPE_ADD;

            $user_wallet_payment->currency = Setting::get('currency') ?? "$";

            $user_wallet_payment->payment_mode = $request->payment_mode ?? CARD;

            $user_wallet_payment->paid_date = date('Y-m-d H:i:s');

            $user_wallet_payment->status = $request->paid_status ?: USER_WALLET_PAYMENT_PAID;

            if($request->file('bank_statement_picture')) {

                $user_wallet_payment->bank_statement_picture = Helper::storage_upload_file($request->file('bank_statement_picture'));

            }

            $user_wallet_payment->message = "";

            $user_wallet_payment->save();

            $user_wallet_payment->message = get_wallet_message($user_wallet_payment);

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

            $charge_array = [
                                'amount' => $total,
                                'currency' => $currency_code,
                                'customer' => $request->customer_id,
                            ];


            $stripe_payment_response =  \Stripe\Charge::create($charge_array);

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

                $user_wallet->total += $user_wallet_payment->paid_amount;

                $user_wallet->remaining += $user_wallet_payment->paid_amount;

            } else {

                $user_wallet->used += $user_wallet_payment->paid_amount;

                $user_wallet->remaining -= $user_wallet_payment->paid_amount;
            }

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

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
     * @param object $subscription_details, object $request
     *
     * @return object $subscription_details
     */

    public static function subscriptions_payment_by_stripe($request, $subscription_details) {

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
            ];


            $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response_array = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $response_array = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response_array, 200);

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
     * @param object $subscription_details, object $request
     *
     * @return object $subscription_details
     */

    public static function subscriptions_payment_save($request, $subscription_details) {

        try {

            $previous_payment = SubscriptionPayment::where('user_id' , $request->id)
                ->where('status', PAID_STATUS)
                ->orderBy('created_at', 'desc')
                ->first();

            $user_subscription_details = new SubscriptionPayment;

            $user_subscription_details->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription_details->plan} months"));

            if($previous_payment) {

                if (strtotime($previous_payment->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {
                    $user_subscription_details->expiry_date = date('Y-m-d H:i:s', strtotime("+{$subscription_details->plan} months", strtotime($previous_payment->expiry_date)));
                }
            }

            $user_subscription_details->subscription_id = $request->subscription_id;

            $user_subscription_details->user_id = $request->id;

            $user_subscription_details->payment_id = $request->payment_id ?? "NO-".rand();

            $user_subscription_details->status = PAID_STATUS;

            $user_subscription_details->amount = $request->paid_amount ?? 0.00;

            $user_subscription_details->payment_mode = $request->payment_mode ?? CARD;

            $user_subscription_details->cancel_reason = $request->cancel_reason ?? '';

            $user_subscription_details->save();

            // update the earnings
            self::users_account_upgrade($request->id, $request->paid_amount, $subscription_details->amount, $user_subscription_details->expiry_date);

            $response_array = ['success' => true, 'message' => 'paid', 'data' => ['user_type' => SUBSCRIBED_USER, 'payment_id' => $request->payment_id]];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $response_array = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response_array, 200);

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

        if($user_details = User::find($user_id)) {

            $user_details->user_type = SUBSCRIBED_USER;

            $user_details->one_time_subscription = $subscription_amount <= 0 ? YES : NO;

            $user_details->amount_paid += $paid_amount ?? 0.00;

            $user_details->expiry_date = $expiry_date;

            $user_details->no_of_days = total_days($expiry_date);

            $user_details->save();
        
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

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
            ];


            $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response_array = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $response_array = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response_array, 200);

        }

    }

    /**
     * @method posts_payment_save()
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

    public static function posts_payment_save($request, $post) {

        try {

            $post_payment = new \App\PostPayment;

            $post_payment->post_id = $request->post_id;

            $post_payment->user_id = $request->id;

            $post_payment->payment_id = $request->payment_id ?? "NO-".rand();

            $post_payment->payment_mode = $request->payment_mode ?? CARD;

            $post_payment->paid_amount = $request->paid_amount ?? 0.00;

            $post_payment->status = PAID;

            $post_payment->save();

            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'payment_id' => $request->payment_id]];

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

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
            ];


            $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response_array = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $response_array = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response_array, 200);

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

            $user_tip->from_user_id = $request->id;

            $user_tip->to_user_id = $request->to_user_id;

            $user_tip->user_card_id = $request->user_card_id ?: 0;

            $user_tip->payment_id = $request->payment_id ?? "NO-".rand();

            $user_tip->payment_mode = $request->payment_mode ?? CARD;

            $user_tip->amount = $request->paid_amount ?? 0.00;

            $user_tip->paid_date = date('Y-m-d H:i:s');

            $user_tip->status = PAID;

            $user_tip->save();

            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'payment_id' => $request->payment_id]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }


}