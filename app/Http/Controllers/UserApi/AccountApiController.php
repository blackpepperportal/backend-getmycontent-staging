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
        
        $this->loginUser = User::where('id',$request->id)->first();

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method register()
     *
     * @uses Registered user can register through manual or social login
     * 
     * @created Akshata
     *
     * @updated 
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

                	$data = User::find($user_details->id);

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
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - User Email & Password
     *
     * @return Json response with user details
     */
    public function login(Request $request) {

        try {

            DB::beginTransaction();

            $rules = 
                [
                    'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                    'device_token' => 'required',
                    'login_by' => 'required|in:manual,facebook,google,apple,linkedin,instagram',
                ];

            Helper::custom_validator($request->all(), $rules);

            /** Validate manual login fields */

            $rules = 
                [
                   'email' => 'required|email',
                    'password' => 'required',
                ];

            Helper::custom_validator($request->all(), $rules);

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

                $data = User::find($user_details->id);
				
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
     * @created Akshata
     *
     * @updated 
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
     * @created Akshata
     *
     * @updated 
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
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function profile(Request $request) {

        try {

            $user_details = User::where('id' , $request->id)->first();

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
     * @created Akshata
     *
     * @updated 
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

            	$data = User::where('id',$user_details->id)->first();

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
     * @created Akshata
     *
     * @updated 
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
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return
     */
    public function logout(Request $request) {

        return $this->sendResponse(api_success(106), 106);

    }

    /**
     * @method notifications_status_update()
     *
     * @uses To enable/disable notifications of email / push notification
     *
     * @created Akshata
     *
     * @updated  
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function notifications_status_update(Request $request) {

        try {

            DB::beginTransaction();

            $rules = ['status' => 'required|numeric']; 

            Helper::custom_validator($request->all(), $rules);
                
            $user_details = User::find($request->id);

            $user_details->email_notification_status = $request->status;

            $user_details->push_notification_status = $request->status;

            $user_details->save();

            $data = \App\User::where('id', $request->id)->first();
            
            DB::commit();

            return $this->sendResponse(api_success(130), 130, $data);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method cards_list()
     *
     * @uses get the user payment mode and cards list
     *
     * @created Akshata
     *
     * @updated
     *
     * @param integer id
     * 
     * @return
     */

    public function cards_list(Request $request) {

        try {

            $user_cards = \App\UserCard::where('user_id' , $request->id)->get();

            $card_payment_mode = $payment_modes = [];

            $card_payment_mode['name'] = "Card";

            $card_payment_mode['payment_mode'] = "card";

            $card_payment_mode['is_default'] = 1;

            array_push($payment_modes , $card_payment_mode);

            $data['payment_modes'] = $payment_modes;   

            $data['cards'] = $user_cards ? $user_cards : []; 

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
    
    /**
     * @method cards_add()
     *
     * @uses used to add card to the user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param card_token
     * 
     * @return JSON Response
     */
    public function cards_add(Request $request) {

        try {

            if(Setting::get('stripe_secret_key')) {

                \Stripe\Stripe::setApiKey(Setting::get('stripe_secret_key'));

            } else {

                throw new Exception(api_error(121), 121);

            }

            // Validation start

            $rules = ['card_token' => 'required'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            
            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(api_error(1002), 1002);
                
            }

            DB::beginTransaction();

            // Get the key from settings table
            
            $customer = \Stripe\Customer::create([
                    // "card" => $request->card_token,
                    "card" => 'tok_visa',
                    "email" => $user_details->email,
                    "description" => "Customer for ".Setting::get('site_name'),
                ]);

            if($customer) {

                $customer_id = $customer->id;

                $card_details = new \App\UserCard;

                $card_details->user_id = $request->id;

                $card_details->customer_id = $customer_id;

                $card_details->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

                $card_details->card_type = $customer->sources->data ? $customer->sources->data[0]->brand : "";

                $card_details->last_four = $customer->sources->data[0]->last4 ? $customer->sources->data[0]->last4 : "";

                $card_details->card_holder_name = $request->card_holder_name ?: $this->loginUser->name;

                // Check is any default is available

                $check_card_details = \App\UserCard::where('user_id',$request->id)->count();

                $card_details->is_default = $check_card_details ? NO : YES;

                if($card_details->save()) {

                    if($user_details) {

                        $user_details->user_card_id = $check_card_details ? $user_details->user_card_id : $card_details->id;

                        $user_details->save();
                    }

                    $data = \App\UserCard::where('id' , $card_details->id)->first();

                    DB::commit();

                    return $this->sendResponse(api_success(105), 105, $data);

                } else {

                    throw new Exception(api_error(114), 114);
                    
                }
           
            } else {

                throw new Exception(api_error(121) , 121);
                
            }

        } catch(Stripe_CardError | Stripe_InvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode() ?: 101);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode() ?: 101);
        }

    }

    /**
     * @method cards_delete()
     *
     * @uses delete the selected card
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param integer user_card_id
     * 
     * @return JSON Response
     */

    public function cards_delete(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                    'user_card_id' => 'required|integer|exists:user_cards,id,user_id,'.$request->id,
                    ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(api_error(1002), 1002);
            }

            \App\UserCard::where('id', $request->user_card_id)->delete();

            if($user_details->payment_mode = CARD) {

                if($check_card = \App\UserCard::where('user_id' , $request->id)->first()) {

                    $check_card->is_default =  DEFAULT_TRUE;

                    $user_details->user_card_id = $check_card->id;

                    $check_card->save();

                } else { 

                    $user_details->payment_mode = COD;

                    $user_details->user_card_id = DEFAULT_FALSE;
                
                }
           
            }

            if($user_details->user_card_id == $request->user_card_id) {

                $user_details->user_card_id = DEFAULT_FALSE;

                $user_details->save();
            }
            
            $user_details->save();
                
            DB::commit();

            return $this->sendResponse(api_success(109), 109, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method cards_default()
     *
     * @uses update the selected card as default
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function cards_default(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                    'user_card_id' => 'required|integer|exists:user_cards,id,user_id,'.$request->id,
                    ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(api_error(1002), 1002);
            }
        
            $old_default_cards = \App\UserCard::where('user_id' , $request->id)->where('is_default', YES)->update(['is_default' => NO]);

            $user_cards = \App\UserCard::where('id' , $request->user_card_id)->update(['is_default' => YES]);

            $user_details->user_card_id = $request->user_card_id;

            $user_details->save();

            DB::commit();

            return $this->sendResponse(api_success(108), 108);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    } 

    /**
     * @method payment_mode_default()
     *
     * @uses update the selected card as default
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function payment_mode_default(Request $request) {

        Log::info("payment_mode_default");

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [

                'payment_mode' => 'required',

            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            $user_details = User::find($request->id);

            $user_details->payment_mode = $request->payment_mode ?: CARD;

            $user_details->save();           

            DB::commit();

            return $this->sendResponse($message = "Mode updated", $code = 200, $data = ['payment_mode' => $request->payment_mode]);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }


}
