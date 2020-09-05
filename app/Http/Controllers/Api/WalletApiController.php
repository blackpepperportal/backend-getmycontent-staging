<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use DB, Log, Hash, Validator, Exception, Setting, Helper;

use App\User;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\WalletRepository as WalletRepo;

class WalletApiController extends Controller
{
    protected $loginUser, $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

        $request->request->add(['timezone' => $this->timezone]);

    }

    /**
     * @method user_wallets_index()
     * 
     * @uses wallet details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_index(Request $request) {

        try {

            $user_wallet = \App\UserWallet::where('user_id', $request->id)->first();
            
            if(!$user_wallet) {

                $user_wallet = \App\UserWallet::create(['user_id' => $request->id, 'total' => 0.00, 'used' => 0.00, 'remaining' => 0.00]);

            }

            $data['user_wallet'] = $user_wallet;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

    /**
     * @method user_wallets_history()
     * 
     * @uses wallet details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_history(Request $request) {

        try {

            $base_query = \App\UserWalletPayment::CommonResponse()->where('user_id', $request->id);

            $history = $base_query->orderBy('created_at', 'desc')->skip($this->skip)->take($this->take)->get();

            $history = WalletRepo::wallets_list_response($history, $request);
            
            $data['history'] = $history ?? [];

            $data['total'] = \App\UserWalletPayment::where('user_id', $request->id)->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

	/**
     * @method user_wallets_add_money_by_stripe()
     * 
     * @uses Delete user account based on user id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Password and user id
     *
     * @return json with boolean output
     */

    public function user_wallets_add_money_by_stripe(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = ['amount' => 'required|numeric|min:1'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $request->request->add(['payment_mode' => CARD]);

            $request->request->add([
                'total' => $request->amount, 
                'user_pay_amount' => $request->amount,
                'paid_amount' => $request->amount,
            ]);

            if($request->amount > 0) {

                // Check the user have the cards

                $card_details = \App\UserCard::where('user_id', $request->id)->where('is_default', YES)->first();

                if(!$card_details) {

                    throw new Exception(api_error(120), 120); 

                }

                $request->request->add(['customer_id' => $card_details->customer_id]);
                
                $card_payment_response = PaymentRepo::user_wallets_payment_by_stripe($request)->getData();

                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => USER_WALLET_PAYMENT_PAID]);

            }

            $payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($payment_response->success) {
                
                DB::commit();

                return $this->sendResponse(api_success(117), 117, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

	/**
     * @method user_wallets_add_money_by_bank_account()
     * 
     * @uses Delete user account based on user id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Password and user id
     *
     * @return json with boolean output
     */

    public function user_wallets_add_money_by_bank_account(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
            	// 'bank_statement_picture' => 'required|mimes:jpg,png,jpeg',
                'payment_id' => 'required',
            	'amount' => 'required|numeric|min:1',
                'user_billing_account_id' => 'nullable|exists:user_billing_accounts,id'
            	];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            if(!$request->user_billing_account_id) {

                $user_billing_account = \App\UserBillingAccount::where('user_id', $request->id)->where('is_default', YES)->first();

                $request->request->add(['user_billing_account_id' => $user_billing_account->user_billing_account_id ?? 0]);
            }

            $request->request->add([
                'payment_mode' => BANK_TRANSFER,
                'total' => $request->amount, 
                'user_pay_amount' => $request->amount,
                'paid_amount' => $request->amount,
                'paid_status' => USER_WALLET_PAYMENT_WAITING
            ]);

            $payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($payment_response->success) {
                
                DB::commit();

                return $this->sendResponse(api_success(133), 133, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }


        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

    /**
     * @method user_wallets_history_for_add()
     * 
     * @uses wallet details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_history_for_add(Request $request) {

        try {

            $base_query = $total_query = \App\UserWalletPayment::CommonResponse()->where('user_id', $request->id)->where('payment_type', WALLET_PAYMENT_TYPE_ADD);

            $history = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();
            
            $data['history'] = $history ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_wallets_history_for_sent()
     * 
     * @uses wallet details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_history_for_sent(Request $request) {

        try {

            $base_query = $total_query = \App\UserWalletPayment::CommonResponse()->where('user_id', $request->id)->where('payment_type', WALLET_PAYMENT_TYPE_PAID);

            $history = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $history = WalletRepo::wallets_list_response($history, $request);
            
            $data['history'] = $history ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_wallets_history_for_received()
     * 
     * @uses wallet details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_history_for_received(Request $request) {

        try {

            $base_query = $total_query = \App\UserWalletPayment::CommonResponse()->where('user_id', $request->id)->where('payment_type', WALLET_PAYMENT_TYPE_CREDIT);

            $history = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $history = WalletRepo::wallets_list_response($history, $request);
            
            $data['history'] = $history ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_wallets_payment_view()
     * 
     * @uses get the selected withdraw request
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_payment_view(Request $request) {

        try {

            $payment_details = \App\UserWalletPayment::CommonResponse()->where('user_wallet_payments.unique_id', $request->user_wallet_payment_unique_id)->first();
            
            if(!$payment_details) {

                throw new Exception(api_error(144), 144);
                
            }

            $data['wallets_payment'] = $payment_details;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


    /**
     * @method user_wallets_send_money()
     * 
     * @uses send money to other user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_send_money(Request $request) {

        try {
            
            DB::beginTransaction();

            // Validation start

            $rules = [
                        'to_user_id' => 'required|exists:users,id', 
                        'amount' => 'required|numeric|min:1'
                    ];

            $custom_errors = ['to_user_id' => api_error(143)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            // Validation end

            // Check the to user is valid for this transaction

            $to_user_details = \App\User::where('status', USER_APPROVED)->where('is_verified', USER_EMAIL_VERIFIED)->where('id', $request->to_user_id)->first();
            
            if(!$to_user_details) {

                throw new Exception(api_error(130), 130);
                
            }

            // Check the user has enough balance 

            $user_wallet_details = \App\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet_details->remaining ?? 0;

            if($remaining < $request->amount) {
                throw new Exception(api_error(131), 131);    
            }
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $request->amount, 
                'user_pay_amount' => $request->amount,
                'paid_amount' => $request->amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'DD-'.rand()
            ]);

            $payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($payment_response->success) {

                // Update the to user

                $to_user_inputs = [
                    'id' => $request->to_user_id,
                    'received_from_user_id' => $request->id,
                    'total' => $request->amount, 
                    'user_pay_amount' => $request->amount,
                    'paid_amount' => $request->amount,
                    'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                    'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                    'payment_id' => 'CD-'.rand()
                ];

                $to_user_request = new \Illuminate\Http\Request();

                $to_user_request->replace($to_user_inputs);

                $to_user_payment_response = PaymentRepo::user_wallets_payment_save($to_user_request)->getData();

                if($to_user_payment_response->success) {


                    $wallet_message = tr('wallet_money_send_message');

                    $wallet_message = str_replace("<%request_amount%>", formatted_amount($request->amount?? '0.00'),$wallet_message);
    
                    $wallet_message = str_replace("<%user_name%>", $user_wallet_details->user->name??'',$wallet_message);

                    $email_data['subject'] = Setting::get('site_name');
    
                    $email_data['page'] = "emails.users.wallet_send_money";
    
                    $email_data['data'] = $user_wallet_details;

                    $email_data['amount'] = formatted_amount($request->amount ?? '0.00');

                    $email_data['email'] = $to_user_details->email ?? '';

                    $email_data['name'] = $to_user_details->name ?? '';
    
                    $email_data['message'] = $wallet_message;
                   
                    $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    DB::commit();

                    return $this->sendResponse(api_success(122), 122, $payment_response->data);

                } else {
                    throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
                }

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method admin_account_details()
     * 
     * @uses get admin account details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function admin_account_details(Request $request) {

        try {

            $admin_account_keys = ['account_number', 'account_holder_name', 'ifsc_code', 'swift_code', 'bank_name', 'branch_name'];

            $data = \App\Settings::whereIn('key', $admin_account_keys)->get();

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    } 
}
