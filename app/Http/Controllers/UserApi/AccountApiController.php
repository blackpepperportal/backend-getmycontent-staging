<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\User;

class AccountApiController extends Controller
{
 	protected $loginUser;

    protected $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::CommonResponse()->find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method register()
     *
     * @uses Registered user can register through manual or social login
     * 
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param Form data
     *
     * @return Json response with user details
     */
    public function register(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = 
                [
                    'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                    'device_token' => 'required',
                    'login_by' => 'required|in:manual,facebook,google,apple,linkedin,instagram',
                ];

            Helper::custom_validator($request->all(), $rules);

            $allowed_social_logins = ['facebook','google','apple', 'linkedin', 'instagram'];

            if(in_array($request->login_by, $allowed_social_logins)) {

                // validate social registration fields

                $rules = [
                    'social_unique_id' => 'required',
                    'name' => 'required|max:255|min:2',
                    'email' => 'required|email|max:255',
                    'mobile' => 'digits_between:6,13',
                    'picture' => '',
                    'gender' => 'in:male,female,others',
                ];

                Helper::custom_validator($request->all(), $rules);

            } else {

                $rules = [
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255|min:2',
                        'password' => 'required|min:6',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                    ];

                Helper::custom_validator($request->all(), $rules);

                // validate email existence

                $rules = ['email' => 'unique:users,email'];

                Helper::custom_validator($request->all(), $rules);

            }

            $user_details = User::where('email' , $request->email)->first();

            $send_email = NO;

            // Creating the user

            if(!$user_details) {

                $user_details = new User;

                register_mobile($request->device_type);

                $send_email = YES;

                $user_details->picture = asset('placeholder.jpeg');

                $user_details->registration_steps = 1;

            } else {

                if(in_array($user_details->status, [USER_PENDING , USER_DECLINED])) {

                    throw new Exception(api_error(1000), 1000);
                
                }

            }

            $user_details->name = $request->name ?? "";

            $user_details->email = $request->email ?? "";

            $user_details->mobile = $request->mobile ?? "";

            if($request->has('password')) {

                $user_details->password = Hash::make($request->password ?: "123456");

            }

            $user_details->gender = $request->has('gender') ? $request->gender : "male";

            $check_device_exist = User::where('device_token', $request->device_token)->first();

            if($check_device_exist) {

                $check_device_exist->device_token = "";

                $check_device_exist->save();
            }

            $user_details->device_token = $request->device_token ?: "";

            $user_details->device_type = $request->device_type ?: DEVICE_WEB;

            $user_details->login_by = $request->login_by ?: 'manual';

            $user_details->social_unique_id = $request->social_unique_id ?: '';

            // Upload picture

            if($request->login_by == 'manual') {

                if($request->hasFile('picture')) {

                    $user_details->picture = Helper::storage_upload_file($request->file('picture') , PROFILE_PATH_USER);

                }

            } else {

                $user_details->picture = $request->picture ?: $user_details->picture;

            }   

            if($user_details->save()) {

                $user_details->save();

                // Send welcome email to the new user:

                if($send_email) {

                    if($user_details->login_by == 'manual') {

                        $user_details->password = $request->password;

                        $subject = tr('user_welcome_title').' '.Setting::get('site_name');

                        $email_data = $user_details;

                        $page = "emails.users.welcome";

                        $email = $user_details->email;

                        $email_send_response = Helper::send_email($page,$subject,$email,$email_data);

                        // No need to throw error. For forgot password we need handle the error response

                        if($email_send_response->success) {

                        } else {

                            $error = $email_send_response->error;

                            Log::info("Registered EMAIL Error".print_r($error , true));
                            
                        }

                    }

                }

                if(in_array($user_details->status , [USER_DECLINED , USER_PENDING])) {
                
                    $response = ['success' => false , 'error' => api_error(1000) , 'error_code' => 1000];

                    DB::commit();

                    return response()->json($response, 200);
               
                }

                if($user_details->is_verified == USER_EMAIL_VERIFIED) {

                	$data = User::CommonResponse()->find($user_details->id);

                    $response = ['success' => true, 'data' => $data];

                } else {

                    $response = ['success' => false, 'error' => api_error(1001), 'error_code'=>1001];

                    DB::commit();

                    return response()->json($response, 200);

                }

            } else {

                throw new Exception(api_error(103), 103);

            }

            DB::commit();

            return response()->json($response, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method login()
     *
     * @uses Registered user can login using their email & password
     * 
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Email & Password
     *
     * @return Json response with user details
     */
    public function login(Request $request) {

        try {

            DB::beginTransaction();

            $basic_validator = Validator::make($request->all(),
                [
                    'device_token' => 'required',
                    'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                    'login_by' => 'required|in:manual,facebook,google,apple,linkedin,instagram',
                ]
            );

            if($basic_validator->fails()){

                $error = implode(',', $basic_validator->messages()->all());

                throw new Exception($error , 101);

            }

            /** Validate manual login fields */

            $manual_validator = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if($manual_validator->fails()) {

                $error = implode(',', $manual_validator->messages()->all());

            	throw new Exception($error , 101);

            }

            $user_details = User::where('email', '=', $request->email)->first();

            $is_email_verified = YES;

            // Check the user details 

            if(!$user_details) {

            	throw new Exception(api_error(1002), 1002);

            }

            // check the user approved status

            if($user_details->status != USER_APPROVED) {

            	throw new Exception(api_error(1000), 1000);

            }

            if(Setting::get('is_account_email_verification') == YES && !$user_details->is_verified) {

                Helper::check_email_verification("" , $user_details->id, $error);

                $is_email_verified = NO;

            }

            if(!$is_email_verified) {

    			throw new Exception(api_error(1001), 1001);
            }

            if(Hash::check($request->password, $user_details->password)) {

                // Generate new tokens
                
                $user_details->token = Helper::generate_token();

                $user_details->token_expiry = Helper::generate_token_expiry();
                
                // Save device details

                $check_device_exist = User::where('device_token', $request->device_token)->first();

                if($check_device_exist) {

                    $check_device_exist->device_token = "";
                    
                    $check_device_exist->save();
                }

                $user_details->device_token = $request->device_token ?? $user_details->device_token;

                $user_details->device_type = $request->device_type ?? $user_details->device_type;

                $user_details->login_by = $request->login_by ?? $user_details->login_by;

                $user_details->save();

                $data = User::CommonResponse()->find($user_details->id);
				
				DB::commit();

            	return $this->sendResponse(api_success(101), 101, $data);

            } else {

				throw new Exception(api_error(102), 102);

            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method forgot_password()
     *
     * @uses If the user forgot his/her password he can hange it over here
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Email id
     *
     * @return send mail to the valid user
     */
    
    public function forgot_password(Request $request) {

        try {

            DB::beginTransaction();

            // Check email configuration and email notification enabled by admin

            if(Setting::get('is_email_notification') != YES ) {

                throw new Exception(api_error(106), 106);
                
            }
            
            $rules = ['email' => 'required|email|exists:users,email']; 

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user_details = User::where('email' , $request->email)->first();

            if(!$user_details) {

                throw new Exception(api_error(1002), 1002);
            }

            if($user_details->login_by != 'manual') {

                throw new Exception(api_error(118), 118);
                
            }

            // check email verification

            if($user_details->is_verified == USER_EMAIL_NOT_VERIFIED) {

                throw new Exception(api_error(1001), 1001);
            }

            // Check the user approve status

            if(in_array($user_details->status , [USER_DECLINED , USER_PENDING])) {
                throw new Exception(api_error(1000), 1000);
            }

            $new_password = Helper::generate_password();

            $user_details->password = Hash::make($new_password);

            // $email_data['subject'] = tr('user_forgot_email_title' , Setting::get('site_name'));

            // $email_data['email']  = $user_details->email;

            // $email_data['password'] = $new_password;

            // $email_data['page'] = "emails.users.forgot-password";

            // $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

            if(!$user_details->save()) {

                throw new Exception(api_error(103));

            }

            DB::commit();

            return $this->sendResponse(api_success(102), $success_code = 102, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method change_password()
     *
     * @uses To change the password of the user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Password & confirm Password
     *
     * @return json response of the user
     */
    public function change_password(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'password' => 'required|confirmed|min:6',
                'old_password' => 'required|min:6',
            ]; 

            Helper::custom_validator($request->all(), $rules, $custom_errors =[]);

            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(api_error(1002), 1002);
            }

            if($user_details->login_by != "manual") {

                throw new Exception(api_error(118), 118);
                
            }

            if(Hash::check($request->old_password,$user_details->password)) {

                $user_details->password = Hash::make($request->password);
                
                if($user_details->save()) {

                    DB::commit();

                    // $email_data['subject'] = tr('change_password_email_title' , Setting::get('site_name'));

                    // $email_data['email']  = $user_details->email;

                    // $email_data['page'] = "emails.users.change-password";

                    // $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    return $this->sendResponse(api_success(104), $success_code = 104, $data = []);
                
                } else {

                    throw new Exception(api_error(103), 103);   
                }

            } else {

                throw new Exception(api_error(108) , 108);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /** 
     * @method profile()
     *
     * @uses To display the user details based on user  id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function profile(Request $request) {

        try {

            $user_details = User::where('id' , $request->id)->CommonResponse()->first();

            if(!$user_details) { 

                throw new Exception(api_error(1002) , 1002);
            }

            return $this->sendResponse($message = "", $success_code = "", $user_details);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
 
    /**
     * @method update_profile()
     *
     * @uses To update the user details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param objecct $request : User details
     *
     * @return json response with user details
     */
    public function update_profile(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
            		'name' => 'required|max:255',
                    'email' => 'email|unique:users,email,'.$request->id.'|max:255',
                    'mobile' => 'digits_between:6,13',
                    // 'picture' => 'mimes:jpeg,bmp,png',
                    'gender' => 'nullable|in:male,female,others',
                    'device_token' => '',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            
            $user_details = User::find($request->id);

            if(!$user_details) { 

                throw new Exception(api_error(1002) , 1002);
            }

            $user_details->name = $request->name ?? $user_details->name;
            
            if($request->has('email')) {

                $user_details->email = $request->email;
            }

            $user_details->mobile = $request->mobile ?: $user_details->mobile;

            $user_details->gender = $request->gender ?: $user_details->gender;

            $user_details->address = $request->address ?: $user_details->address;

            // Upload picture
            if($request->hasFile('picture') != "") {

                Helper::storage_delete_file($user_details->picture, COMMON_FILE_PATH); // Delete the old pic

                $user_details->picture = Helper::storage_upload_file($request->file('picture') , COMMON_FILE_PATH);

            }

            if($user_details->save()) {

            	$data = User::CommonResponse()->find($user_details->id);

                DB::commit();

                return $this->sendResponse($message = api_success(111), $success_code = 111, $data);

            } else {    

        		throw new Exception(api_error(103), 103);
            }

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method delete_account()
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

    public function delete_account(Request $request) {

        try {

            DB::beginTransaction();

            $request->request->add([ 
                'login_by' => $this->loginUser ? $this->loginUser->login_by : "manual",
            ]);

            // Validation start

            $rules = ['password' => 'required_if:login_by,manual'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $user_details = User::find($request->id);

            if(!$user_details) {

            	throw new Exception(api_error(1002), 1002);
                
            }

            // The password is not required when the user is login from social. If manual means the password is required

            if($user_details->login_by == 'manual') {

                if(!Hash::check($request->password, $user_details->password)) {
         
                    throw new Exception(api_error(104), 104); 
                }
            
            }

            if($user_details->delete()) {

                DB::commit();

                return $this->sendResponse(api_success(103), $success_code = 103, $data = []);

            } else {

            	throw new Exception(api_error(119), 119);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

    /**
     * @method logout()
     *
     * @uses Logout the user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     * 
     * @return
     */
    public function logout(Request $request) {

        return $this->sendResponse(api_success(106), 106);

    }

    /**
     * @method subscriptions_index()
     *
     * @uses To display all the subscription plans
     *
     * @created vithya R
     *
     * @updated Vidhya R
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function subscriptions_index(Request $request) {

        try {

            $subscriptions = Subscription::Approved()->orderBy('amount', 'asc')->get();

            $data['subscriptions'] = $subscriptions ?? [];

            $data['total'] = $subscriptions->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method subscriptions_view()
     *
     * @uses get the selected subscription details
     *
     * @created vithya R
     *
     * @updated Vidhya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function subscriptions_view(Request $request) {

        try {

            $rules = ['subscription_id' => 'required|exists:subscriptions,id',$request->subscription_id];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $subscription_details = Subscription::BaseResponse()->where('subscriptions.status' , APPROVED)->where('subscriptions.id', $request->subscription_id)->first();

            if(!$subscription_details) {
                throw new Exception(api_error(135), 135);   
            }

            return $this->sendResponse($message = '' , $code = '', $subscription_details);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method subscriptions_history()
     *
     * @uses get the selected subscription details
     *
     * @created vithya R
     *
     * @updated Vidhya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function subscriptions_history(Request $request) {

        try {

            $user_subscriptions = UserSubscription::BaseResponse()->where('user_id' , $request->id)->skip($this->skip)->take($this->take)->orderBy('user_subscriptions.id', 'desc')->get();

            foreach ($user_subscriptions as $key => $value) {

                $value->plan_text = formatted_plan($value->plan ?? 0);

                $value->expiry_date = common_date($value->expiry_date, $this->timezone ?? '', 'd M Y');

                $value->no_of_users_formatted = no_of_users_formatted($value->no_of_users);

                $value->no_of_hrs_formatted = no_of_hrs_formatted($value->no_of_hrs, $value->no_of_hrs_type);
            
            }

            return $this->sendResponse($message = '' , $code = '', $user_subscriptions);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /** 
     * @method subscriptions_payment_by_card()
     *
     * @uses pay for subscription using paypal
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function subscriptions_payment_by_card(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                    'subscription_id' => 'required|exists:subscriptions,id',
                    ];

            $custom_errors = ['subscription_id' => api_error(151)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $subscription_details = Subscription::where('id',  $request->subscription_id)
                                    ->Approved()
                                    ->first();

            if(!$subscription_details) {

                throw new Exception(api_error(161), 161);
                
            }

            $request->request->add(['payment_mode' => CARD]);

            $total = $user_pay_amount = $subscription_details->amount ?? 0.00;


            $request->request->add([
                'total' => $total, 
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount,
            ]);

            if($user_pay_amount > 0) {

                // Check the user have the cards

                $card_details = \App\UserCard::where('user_id', $request->id)->where('is_default', YES)->first();

                // If the user doesn't have cards means the payment will switch to COD

                if(!$card_details) {

                    throw new Exception(api_error(163), 163); 

                }

                $request->request->add(['customer_id' => $card_details->customer_id]);
                
                $card_payment_response = PaymentRepo::subscriptions_payment_by_stripe($request, $subscription_details)->getData();

                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }

            $payment_response = PaymentRepo::subscriptions_payment_save($request, $subscription_details)->getData();

            if($payment_response->success) {
                
                DB::commit();

                $code = 111;

                return $this->sendResponse(api_success($code), $code, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method subscriptions_payment_by_paypal()
     *
     * @uses pay for subscription using paypal
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function subscriptions_payment_by_paypal(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                    'subscription_id' => 'required|exists:subscriptions,id',
                    'payment_id' => 'required',
                    ];

            $custom_errors = ['subscription_id' => api_error(151)];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

           // Check the subscription is available

            $subscription_details = Subscription::where('id',  $request->subscription_id)
                                    ->Approved()
                                    ->first();

            if(!$subscription_details) {

                throw new Exception(api_error(161), 161);
                
            }

            $request->request->add(['payment_mode' => PAYPAL]);

            $total = $user_pay_amount = $subscription_details->amount ?? 0.00;

            $request->request->add([
                'total' => $total, 
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount,
            ]);

            $payment_response = PaymentRepo::subscriptions_payment_save($request, $subscription_details)->getData();

            if($payment_response->success) {
                
                DB::commit();

                $code = 111;

                return $this->sendResponse(api_success($code), $code, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

}
