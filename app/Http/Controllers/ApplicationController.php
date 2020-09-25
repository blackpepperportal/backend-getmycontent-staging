<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log, Validator, Exception, DB, Setting;

use App\Helpers\Helper;

use App\StaticPage;

class ApplicationController extends Controller
{

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

    public function static_pages_api(Request $request) {

        if($request->page_type) {

            $static_page = StaticPage::where('type' , $request->page_type)
                                ->where('status' , APPROVED)
                                ->select('id as page_id' , 'title' , 'description','type as page_type', 'status' , 'created_at' , 'updated_at')
                                ->first();

            $response_array = ['success' => true , 'data' => $static_page];

        } else {

            $static_pages = StaticPage::Approved()
                ->orderBy('id' , 'asc')
                ->orderBy('title', 'asc')
                ->get();

            $response_array = ['success' => true , 'data' => $static_pages ? $static_pages->toArray(): []];

        }
        
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



public function subscriptions_autorenewal(Request $request){

        try {
            $current_timestamp = \Carbon\Carbon::now()->toDateTimeString();
            $subscriptionDetails = SubscriptionPayment::where('is_current_subscription',1)->where('expiry_date','<', $current_timestamp)->get();

            if(!$subscriptionDetails) {

                throw new Exception(api_error(129), 129);

            }

            foreach ($subscriptionDetails as $subscription_details){

                $user_details = User::where('id',  $subscription_details->user_id)->first();

                if ($user_details->one_time_subscription == 1){
                    DB::beginTransaction();

                    // Validation start

                    $rules = [
                        'subscription_id' => 'required|exists:subscriptions,id',
                    ];

                    $custom_errors = ['subscription_id' => api_error(129)];

                    Helper::custom_validator($subscription_details->all(), $rules, $custom_errors);

                    // Validation end

                    // Check the subscription is available

                    $subscription_details = Subscription::Approved()->firstWhere('id',  $subscription_details->subscription_id);

                    $is_user_subscribed_free_plan = $this->loginUser->one_time_subscription ?? NO;

                    if($subscription_details->amount <= 0 && $is_user_subscribed_free_plan) {

                        throw new Exception(api_error(130), 130);

                    }

                    $request->request->add(['payment_mode' => CARD]);

                    $total = $user_pay_amount = $subscription_details->amount ?? 0.00;

                    if($user_pay_amount > 0) {

                        $card_details = \App\UserCard::where('user_id', $subscription_details->id)->firstWhere('is_default', YES);

                        if(!$card_details) {

                            throw new Exception(api_error(120), 120);

                        }

                        $request->request->add([
                            'total' => $total,
                            'customer_id' => $card_details->customer_id,
                            'user_pay_amount' => $user_pay_amount,
                            'paid_amount' => $user_pay_amount,
                        ]);


                        $card_payment_response = PaymentRepo::subscriptions_payment_by_stripe($request, $subscription_details)->getData();

                        if($card_payment_response->success == false) {

                            throw new Exception($card_payment_response->error, $card_payment_response->error_code);

                        }

                        $card_payment_data = $card_payment_response->data;

                        $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

                    }

                    $payment_response = PaymentRepo::subscriptions_payment_save($request, $subscription_details)->getData();

                    if($payment_response->success) {
                        $data['subscription_id']=$subscription_details->subscription_id;
                        $data['user_id']=$subscription_details->user_id;
                        $data['payment_id']=$card_payment_data->payment_id;
                        $data['is_current_subscription']=1;
                        if ($subscription_details->plan_type == 'months')
                            $data['expiry_date']=Carbon::now()->addMonths(1);
                        else
                            $data['expiry_date']=Carbon::now()->addYears(1);
                        $data['paid_date']=$current_timestamp;
                        $data['created_at']=$current_timestamp;
                        $data['updated_at']=$current_timestamp;

                        $renew = SubscriptionPayment::insert($data);
                        DB::commit();

                        $code = 118;

                        return $this->sendResponse(api_success($code), $code, $payment_response->data);

                    } else {

                        throw new Exception($payment_response->error, $payment_response->error_code);

                    }
                }

            }


        }catch (Exception $e){
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }
    

}
