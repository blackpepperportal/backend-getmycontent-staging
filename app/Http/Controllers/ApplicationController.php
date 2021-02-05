<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log, Validator, Exception, DB, Setting;

use App\Helpers\Helper;

use App\StaticPage;

use App\SubscriptionPayment;

use App\User;

use App\Subscription;

use App\Repositories\PaymentRepository as PaymentRepo;

class ApplicationController extends Controller
{
    /**
     * @method static_pages_api()
     *
     * @uses to get the pages
     *
     * @created Vidhya R 
     *
     * @edited Vidhya R
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function static_pages_api(Request $request) {

        $base_query = \App\StaticPage::where('status', APPROVED)->orderBy('title', 'asc');
                                
        if($request->page_type) {

            $static_pages = $base_query->where('type' , $request->page_type)->first();

        } elseif($request->page_id) {

            $static_pages = $base_query->where('id' , $request->page_id)->first();

        } elseif($request->unique_id) {

            $static_pages = $base_query->where('unique_id' , $request->unique_id)->first();

        } else {

            $static_pages = $base_query->get();

        }

        $response_array = ['success' => true , 'data' => $static_pages ? $static_pages->toArray(): []];

        return response()->json($response_array , 200);

    }

    /**
     * @method static_pages_api()
     *
     * @uses to get the pages
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function static_pages_web(Request $request) {

        $static_page = StaticPage::where('unique_id' , $request->unique_id)
                            ->Approved()
                            ->first();

        $response_array = ['success' => true , 'data' => $static_page];

        return response()->json($response_array , 200);

    }

    /**
     * @method subscription_payments_autorenewal()
     *
     * @uses to get the pages
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param - 
     *
     * @return JSON Response
     */


    public function subscription_payments_autorenewal(Request $request){

        try {

            $current_timestamp = \Carbon\Carbon::now()->toDateTimeString();

            $subscription_payments = SubscriptionPayment::where('is_current_subscription',1)->where('expiry_date','<', $current_timestamp)->get();

            if($subscription_payments->isEmpty()) {

                throw new Exception(api_error(129), 129);

            }
            DB::beginTransaction();
            foreach ($subscription_payments as $subscription_payment){

                $user = User::where('id',  $subscription_payment->user_id)->first();

                if ($user){
                    
                    // Check the subscription is available

                    $subscription = Subscription::Approved()->firstWhere('id',  $subscription_payment->subscription_id);

                    if(!$subscription) {

                        throw new Exception(api_error(129), 129);

                     }

                    
                    $is_user_subscribed_free_plan = $this->loginUser->one_time_subscription ?? NO;

                    if($subscription->amount <= 0 && $is_user_subscribed_free_plan) {

                        throw new Exception(api_error(130), 130);

                    }

                    $payment['payment_mode'] = CARD;

                    $total = $user_pay_amount = $subscription->amount;

                    $card = \App\UserCard::where('user_id', $subscription->id)->firstWhere('is_default', YES);

                    if(!$card) {

                          throw new Exception(api_error(120), 120);

                     }

                    $request->request->add([
                    'total' => $total, 
                    'customer_id' => $card->customer_id,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);

                     $card_payment_response = PaymentRepo::subscriptions_payment_by_stripe($request, $subscription)->getData();

                    if($card_payment_response->success == false) {

                          throw new Exception($card_payment_response->error, $card_payment_response->error_code);

                     }

                     $card_payment_data = $card_payment_response->data;

                     $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'subscription_id' => $subscription->id, 'paid_status' => $card_payment_data->paid_status]);


                    $payment_response = PaymentRepo::subscriptions_payment_save($request, $subscription)->getData();

                    if($payment_response->success) {

                        // Change old status to expired

                        SubscriptionPayment::where('id', $subscription_payment->id)->update(['is_current_subscription' => 0]);

                        // Change new is_current_subscription to 1 

                        SubscriptionPayment::where('payment_id', $payment_response->data->payment_id)->update(['is_current_subscription' => 1]);

                        $code = 118;

                        return $this->sendResponse(api_success($code), $code, $payment_response->data);

                    } else {

                        throw new Exception($payment_response->error, $payment_response->error_code);

                    }
                }else{

                throw new Exception(api_error(135), 135);

            }
            }

            DB::commit();


        }catch (Exception $e){
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method chat_messages_save()
     * 
     * @uses - To save the chat message.
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function chat_messages_save(Request $request) {

        try {

            Log::info("message_save".print_r($request->all() , true));

            $rules = [
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|exists:users,id',
                'message' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);
            
            $message = $request->message;

            $from_chat_user_inputs = ['from_user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id];

            $from_chat_user = \App\ChatUser::updateOrCreate($from_chat_user_inputs);

            $to_chat_user_inputs = ['from_user_id' => $request->to_user_id, 'to_user_id' => $request->from_user_id];

            $to_chat_user = \App\ChatUser::updateOrCreate($to_chat_user_inputs);

            // $from_chat_user = \App\ChatUser::where('from_user_id', $request->from_user_id)->where('to_user_id', $request->to_user_id)->first();

            // if(!$from_chat_user) {

            //     $chat_user = \App\ChatUser::createor(['from_user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id]);

            // }

            // $to_chat_user = \App\ChatUser::where('from_user_id', $request->to_user_id)->where('to_user_id', $request->from_user_id)->first();

            // if(!$to_chat_user) {

            //     $chat_user = \App\ChatUser::create(['from_user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id]);

            // }

            $chat_message = new \App\ChatMessage;

            $chat_message->from_user_id = $request->from_user_id;

            $chat_message->to_user_id = $request->to_user_id;

            $chat_message->message = $request->message;

            $chat_message->save();

            DB::commit();

            return $this->sendResponse("", "", $chat_message);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method settings_generate_json()
     * 
     * @uses
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function settings_generate_json(Request $request) {

        try {

            Helper::settings_generate_json();

            return $this->sendResponse("", "", $data = []);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }


    /**
     * @method chat_messages_save()
     * 
     * @uses - To save the chat message.
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function get_notifications_count(Request $request) {

        try {

            Log::info("Notification".print_r($request->all(),true));

            $rules = [
                'user_id' => 'required|exists:users,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            $chat_message = \App\ChatMessage::where('to_user_id', $request->user_id)->where('status',NO);

            $bell_notification = \App\BellNotification::where('to_user_id', $request->user_id)->where('is_read',BELL_NOTIFICATION_STATUS_UNREAD);

            $data['chat_notification'] = $chat_message->count() ?: 0;

            $data['bell_notification'] = $bell_notification->count() ?: 0;

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }
}
