<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\User;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\CommonRepository as CommonRepo;

use Carbon\Carbon;


class UserAccountApiController extends Controller
{
 	protected $loginUser;

    protected $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method register()
     *
     * @uses Registered user can register through manual or social login
     * 
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param Form data
     *
     * @return Json response with user details
     */
    public function register(Request $request) {
        try {

            DB::beginTransaction();

            $rules = [
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                'device_token' => '',
                'login_by' => 'required|in:manual,facebook,google,apple,linkedin,instagram',
            ];

            Helper::custom_validator($request->all(), $rules);

            $allowed_social_logins = ['facebook', 'google', 'apple', 'linkedin', 'instagram'];

            if(in_array($request->login_by, $allowed_social_logins)) {

                // validate social registration fields
                $rules = [
                    'social_unique_id' => 'required',
                    'first_name' => 'nullable|max:255|min:2',
                    'last_name' => 'nullable|max:255|min:1',
                    'email' => 'required|email|max:255',
                    'mobile' => 'nullable|digits_between:6,13',
                    'picture' => '',
                    'gender' => 'nullable|in:male,female,others',
                ];

                Helper::custom_validator($request->all(), $rules);

            } else {

                $rules = [
                        'first_name' => 'required|max:255|min:2',
                        'last_name' => 'required|max:255|min:1',
                        'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|max:255|min:2',
                        'password' => 'required|min:6',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                        'u_category_id' => 'nullable|integer|exists:u_categories,id',
                    ];

                Helper::custom_validator($request->all(), $rules);
                // validate email existence

                $rules = ['email' => 'unique:users,email'];

                Helper::custom_validator($request->all(), $rules);

            }

            $user = User::firstWhere('email' , $request->email);

            $send_email = NO;

            // Creating the user

            if(!$user) {

                $user = new User;

                register_mobile($request->device_type);

                $send_email = YES;

                $user->registration_steps = 1;

            } else {

                if(in_array($user->status, [USER_PENDING , USER_DECLINED])) {

                    throw new Exception(api_error(1000), 1000);
                
                }

            }

            $user->first_name = $request->first_name ?? "";

            $user->last_name = $request->last_name ?? "";

            $user->email = $request->email ?? "";

            $user->mobile = $request->mobile ?? "";

            if($request->has('password')) {

                $user->password = Hash::make($request->password ?: "123456");

            }

            $user->gender = $request->gender ?? "male";

            $check_device_exist = User::firstWhere('device_token', $request->device_token);

            if($check_device_exist) {

                $check_device_exist->device_token = "";

                $check_device_exist->save();
            }

            $user->device_token = $request->device_token ?: "";

            $user->device_type = $request->device_type ?: DEVICE_WEB;

            $user->login_by = $request->login_by ?: 'manual';

            $user->social_unique_id = $request->social_unique_id ?: '';

            // Upload picture

            if($request->login_by == 'manual') {

                if($request->hasFile('picture')) {

                    $user->picture = Helper::storage_upload_file($request->file('picture') , PROFILE_PATH_USER);

                }

            } else {

                $user->picture = $request->picture ?: $user->picture;

            }   

            if($user->save()) {

                // Send welcome email to the new user:

                if($request->u_category_id){

                  $ucategory = \App\UserCategory::where('u_category_id',$request->u_category_id)->where('user_id', $user->id)->first() ?? new \App\UserCategory;

                  $ucategory->u_category_id = $request->u_category_id;

                  $ucategory->user_id = $user->id;

                  $ucategory->save();
                }

                if($send_email) {

                    if($user->login_by == 'manual') {

                        $email_data['subject'] = tr('user_welcome_title').' '.Setting::get('site_name');

                        $email_data['page'] = "emails.users.welcome";

                        $email_data['data'] = $user;

                        $email_data['email'] = $user->email;

                        $email_data['name'] = $user->name;

                        $email_data['verification_code'] = $user->verification_code;

                        $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    }

                }

                if(in_array($user->status , [USER_DECLINED , USER_PENDING])) {
                
                    $response = ['success' => false , 'error' => api_error(1000) , 'error_code' => 1000];

                    DB::commit();

                    return response()->json($response, 200);
               
                }

                if($user->is_email_verified == USER_EMAIL_VERIFIED) {

                    counter(); // For site analytics. Don't remove
                    
                    $data = User::find($user->id);

                    $response = ['success' => true, 'message' => api_success(101), 'data' => $data];

                } else {

                    $data = User::find($user->id);

                    $response = ['success' => true, 'message' => api_error(1001), 'code' => 1001, 'data' => $data];

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
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - User Email & Password
     *
     * @return Json response with user details
     */
    public function login(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'device_token' => 'nullable',
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                'login_by' => 'required|in:manual,facebook,google,apple,linkedin,instagram',
            ];

            Helper::custom_validator($request->all(), $rules);

            $rules = [
                'email' => 'required|email',
                'password' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules);

            $user = User::firstWhere('email', '=', $request->email);

            $is_email_verified = YES;

            // Check the user details 

            if(!$user) {

                throw new Exception(api_error(1002), 1002);

            }

            // check the user approved status

            if($user->status != USER_APPROVED) {

                throw new Exception(api_error(1000), 1000);

            }

            if($user->is_email_verified != USER_EMAIL_VERIFIED) {

                $data = User::find($user->id);

                $response = ['success' => true, 'message' => api_error(1001), 'code' => 1001, 'data' => $data];

                return response()->json($response, 200);

            }

            if(Hash::check($request->password, $user->password)) {

                // Generate new tokens
                
                // $user->token = Helper::generate_token();

                $user->token_expiry = Helper::generate_token_expiry();
                
                // Save device details

                $check_device_exist = User::firstWhere('device_token', $request->device_token);

                if($check_device_exist) {

                    $check_device_exist->device_token = "";
                    
                    $check_device_exist->save();
                }

                $user->device_token = $request->device_token ?? $user->device_token;

                $user->device_type = $request->device_type ?? $user->device_type;

                $user->login_by = $request->login_by ?? $user->login_by;

                $user->save();

                $data = User::find($user->id);

                DB::commit();
                
                counter(); // For site analytics. Don't remove

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
     * @created Bhawya N 
     *
     * @updated Bhawya N
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

            $user = User::firstWhere('email' , $request->email);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }

            if($user->login_by != 'manual') {

                throw new Exception(api_error(118), 118);
                
            }

            // check email verification

            if($user->is_email_verified == USER_EMAIL_NOT_VERIFIED) {

                throw new Exception(api_error(1001), 1001);
            }

            // Check the user approve status

            if(in_array($user->status , [USER_DECLINED , USER_PENDING])) {
                throw new Exception(api_error(1000), 1000);
            }

            $token = app('auth.password.broker')->createToken($user);

            \App\PasswordReset::where('email', $user->email)->delete();

            \App\PasswordReset::insert([
                'email'=>$user->email,
                'token'=>$token,
                'created_at'=>Carbon::now()
            ]);

            $email_data['subject'] = tr('reset_password_title' , Setting::get('site_name'));

            $email_data['email']  = $user->email;

            $email_data['name']  = $user->name;

            $email_data['page'] = "emails.users.forgot-password";

            $email_data['url'] = Setting::get('frontend_url')."/resetpassword/".$token;
            
            $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

            DB::commit();

            return $this->sendResponse(api_success(102), $success_code = 102, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }


    /**
     * @method reset_password()
     *
     * @uses To reset the password
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param object $request - Email id
     *
     * @return send mail to the valid user
     */
    
    public function reset_password(Request $request) {

        try {

            $rules = [
                'password' => 'required|confirmed|min:6',
                'token' => 'required|string',
                'password_confirmation'=>'required'
            ]; 

            Helper::custom_validator($request->all(), $rules, $custom_errors =[]);

            DB::beginTransaction();

            $password_reset = \App\PasswordReset::where('token', $request->token)->first();

            if(!$password_reset){

                throw new Exception(api_error(163), 163);
            }
            
            $user = User::where('email', $password_reset->email)->first();

            $user->password = \Hash::make($request->password);

            $user->save();

            \App\PasswordReset::where('email', $user->email) ->delete();

            DB::commit();

            return $this->sendResponse(api_success(153), $success_code = 153, $data = []);

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
     * @created Bhawya N 
     *
     * @updated Bhawya N
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

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }

            if($user->login_by != "manual") {

                throw new Exception(api_error(118), 118);
                
            }

            if(Hash::check($request->old_password,$user->password)) {

                $user->password = Hash::make($request->password);
                
                if($user->save()) {

                    DB::commit();

                    $email_data['subject'] = tr('change_password_email_title' , Setting::get('site_name'));

                    $email_data['email']  = $user->email;

                    $email_data['page'] = "emails.users.change-password";

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
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function profile(Request $request) {

        try {

            $user = User::firstWhere('id' , $request->id);

            if(!$user) { 

                throw new Exception(api_error(1002) , 1002);
            }

            $user->updated_formatted = common_date($user->updated_at, $this->timezone, 'd M Y');

            $user->monthly_amount = $user->userSubscription->monthly_amount ?? 0.00;

            $user->yearly_amount = $user->userSubscription->yearly_amount ?? 0.00;

            return $this->sendResponse($message = "", $success_code = "", $user);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
 
    /**
     * @method update_profile()
     *
     * @uses To update the user details
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
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
                    'first_name' => 'nullable|max:255',
                    'last_name' => 'nullable|max:255',
                    'email' => 'email|unique:users,email,'.$request->id.'|regex:/(.+)@(.+)\.(.+)/i|max:255',
                    'username' => 'nullable|unique:users,username,'.$request->id.'|max:255',
                    'mobile' => 'nullable|digits_between:6,13',
                    'picture' => 'nullable|mimes:jpeg,jpg,bmp,png',
                    'cover' => 'nullable|mimes:jpeg,jpg,bmp,png',
                    'gender' => 'nullable|in:male,female,others',
                    'device_token' => '',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            
            $user = User::find($request->id);

            if(!$user) { 

                throw new Exception(api_error(1002) , 1002);
            }

            $user->name = $request->name ?: $user->name;

            $username = $request->username ?: $user->username;

            $user->unique_id = $user->username = routefreestring(strtolower($username));

            $user->first_name = $request->first_name ?: $user->first_name;

            $user->last_name = $request->last_name ?: $user->last_name;
            
            if($request->has('email')) {

                $user->email = $request->email;
            }

            $user->mobile = $request->mobile ?: $user->mobile;

            $user->about = $request->filled('about') ? $request->about : "";

            $user->gender = $request->filled('gender') ? $request->gender : 'male';

            $user->address = $request->filled('address') ? $request->address : "";

            $user->website = $request->filled('website') ? $request->website : "";

            $user->amazon_wishlist = $request->filled('amazon_wishlist') ? $request->amazon_wishlist : "";

            // Upload picture
            if($request->hasFile('picture') != "") {

                Helper::storage_delete_file($user->picture, PROFILE_PATH_USER); // Delete the old pic

                $user->picture = Helper::storage_upload_file($request->file('picture'), PROFILE_PATH_USER);
            
            }

            if($request->hasFile('cover') != "") {

                Helper::storage_delete_file($user->cover, PROFILE_PATH_USER); // Delete the old pic

                $user->cover = Helper::storage_upload_file($request->file('cover'), PROFILE_PATH_USER);
            
            }

            if($user->save()) {

                $data = User::find($user->id);

                DB::commit();

                if($request->monthly_amount || $request->yearly_amount) {

                    // Check the user is eligibility

                    $account_response = \App\Repositories\CommonRepository::user_premium_account_check($user)->getData();

                    if(!$account_response->success) {

                        throw new Exception($account_response->error, $account_response->error_code);
                    }

                    $user_subscription = \App\UserSubscription::where('user_id', $request->id)->first() ?? new \App\UserSubscription;

                    $user_subscription->user_id = $request->id;

                    $user_subscription->monthly_amount = $request->monthly_amount ?: ($user_subscription->monthly_amount ?: 0.00);

                    $user_subscription->yearly_amount = $request->yearly_amount ?: ($user_subscription->yearly_amount ?: 0.00);

                    $user_subscription->save();

                    $user->user_account_type = USER_PREMIUM_ACCOUNT;

                    $user->save();

                    DB::commit();

                }

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
     * @created Bhawya N 
     *
     * @updated Bhawya N
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

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
                
            }

            // The password is not required when the user is login from social. If manual means the password is required

            if($user->login_by == 'manual') {

                if(!Hash::check($request->password, $user->password)) {
         
                    throw new Exception(api_error(104), 104); 
                }
            
            }

            if($user->delete()) {

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
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param 
     * 
     * @return
     */
    public function logout(Request $request) {

        return $this->sendResponse(api_success(106), 106);

    }

    /**
     * @method cards_list()
     *
     * @uses get the user payment mode and cards list
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
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
     * @created Bhawya N
     *
     * @updated Bhawya N
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
            
            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
                
            }

            DB::beginTransaction();

            // Get the key from settings table

            $customer = \Stripe\Customer::create([
                    // "card" => $request->card_token,
                    // "card" => 'tok_visa',
                    "email" => $user->email,
                    "description" => "Customer for ".Setting::get('site_name'),
                    // 'payment_method' => $request->card_token,
                    // 'default_payment_method'
                    // 'source' => $request->card_token
                ]);

            $stripe = new \Stripe\StripeClient(Setting::get('stripe_secret_key'));

            $intent = \Stripe\SetupIntent::create([
              'customer' => $customer->id,
              'payment_method' => $request->card_token
            ]);

            $stripe->setupIntents->confirm($intent->id,['payment_method' => $request->card_token]);


            $retrieve = $stripe->paymentMethods->retrieve($request->card_token, []);
            
            $card_info_from_stripe = $retrieve->card ? $retrieve->card : [];

            \Log::info("card_info_from_stripe".print_r($card_info_from_stripe, true));

            if($customer && $card_info_from_stripe) {

                $customer_id = $customer->id;

                $card = new \App\UserCard;

                $card->user_id = $request->id;

                $card->customer_id = $customer_id;

                $card->card_token = $request->card_token ?? "NO-TOKEN";

                $card->card_type = $card_info_from_stripe->brand ?? "";

                $card->last_four = $card_info_from_stripe->last4 ?? '';

                $card->card_holder_name = $request->card_holder_name ?: $this->loginUser->name;

                // $cards->month = $card_details_from_stripe->exp_month ?? "01";

                // $cards->year = $card_details_from_stripe->exp_year ?? "01";

                // Check is any default is available

                $check_card = \App\UserCard::where('user_id',$request->id)->count();

                $card->is_default = $check_card ? NO : YES;

                if($card->save()) {

                    if($user) {

                        // $user->user_card_id = $check_card ? $user->user_card_id : $card->id;

                        $user->save();
                    }

                    $data = \App\UserCard::firstWhere('id' , $card->id);

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
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer user_card_id
     * 
     * @return JSON Response
     */

    public function cards_delete(Request $request) {

        try {

            DB::beginTransaction();

            // validation start

            $rules = [
                'user_card_id' => 'required|integer|exists:user_cards,id,user_id,'.$request->id,
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // validation end

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }

            \App\UserCard::where('id', $request->user_card_id)->delete();

            if($user->payment_mode = CARD) {

                // Check he added any other card

                if($check_card = \App\UserCard::firstWhere('user_id' , $request->id)) {

                    $check_card->is_default =  DEFAULT_TRUE;

                    $user->user_card_id = $check_card->id;

                    $check_card->save();

                } else { 

                    $user->payment_mode = COD;

                    $user->user_card_id = DEFAULT_FALSE;
                
                }
           
            }

            // Check the deleting card and default card are same

            if($user->user_card_id == $request->user_card_id) {

                $user->user_card_id = DEFAULT_FALSE;

                $user->save();
            }
            
            $user->save();
                
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
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function cards_default(Request $request) {

        try {

            DB::beginTransaction();

            // validation start

            $rules = [
                'user_card_id' => 'required|integer|exists:user_cards,id,user_id,'.$request->id,
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // validation end

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }
        
            $old_default_cards = \App\UserCard::where('user_id' , $request->id)->where('is_default', YES)->update(['is_default' => NO]);

            $user_cards = \App\UserCard::where('id' , $request->user_card_id)->update(['is_default' => YES]);

            $user->user_card_id = $request->user_card_id;

            $user->save();

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
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function payment_mode_default(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [

                'payment_mode' => 'required',

            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            $user = User::find($request->id);

            $user->payment_mode = $request->payment_mode ?: CARD;

            $user->save();           

            DB::commit();

            return $this->sendResponse($message = "Mode updated", $code = 200, $data = ['payment_mode' => $request->payment_mode]);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method user_premium_account_check()
     *
     * @uses check the user is eligiable for the premium acounts
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function user_premium_account_check(Request $request) {

        try {

            $user = User::find($request->id);

            $account_response = \App\Repositories\CommonRepository::user_premium_account_check($user)->getData();

            if(!$account_response->success) {

                throw new Exception($account_response->error, $account_response->error_code);
            }           

            return $this->sendResponse($message = $account_response->message, $code = 200, $data = $user);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method regenerate_email_verification_code()
     *
     * @uses 
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function regenerate_email_verification_code(Request $request) {

        try {

            DB::beginTransaction();

            $user = \App\User::find($request->id);

            $user->verification_code = Helper::generate_email_code();

            $user->verification_code_expiry = \Helper::generate_email_expiry();

            $user->save();

            $email_data['subject'] = Setting::get('site_name');

            $email_data['page'] = "emails.users.verification-code";

            $email_data['data'] = $user;

            $email_data['email'] = $user->email;

            $email_data['verification_code'] = $user->verification_code;

            $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

            DB::commit();

            return $this->sendResponse($message = api_success(147), $code = 147, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method verify_email()
     *
     * @uses 
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function verify_email(Request $request) {

        try {

            DB::beginTransaction();
            
            $rules = ['verification_code' => 'required|min:6|max:6'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\User::find($request->id);

            if($user->verification_code != $request->verification_code) {

                throw new Exception(api_error(146), 146);

            }

            $user->is_verified = USER_EMAIL_VERIFIED;

            $user->save();

            DB::commit();

            $data = User::CommonResponse()->find($user->id);

            return $this->sendResponse($message = api_success(148), $code = 148, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method notifications_status_update()
     *
     * @uses To enable/disable notifications of email / push notification
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
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
                
            $user = User::find($request->id);

            $user->is_email_notification = $user->is_push_notification = $request->status;

            $user->save();

            $data = \App\User::firstWhere('id', $request->id);
            
            DB::commit();

            return $this->sendResponse(api_success(130), 130, $data);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /** 
     * @method user_billing_accounts_list()
     *
     * @uses To list user billing accounts
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param
     *
     * @return json response with details
     */

    public function user_billing_accounts_list(Request $request) {

        try {

            $user_billing_accounts = \App\UserBillingAccount::where('user_id', $request->id)->CommonResponse()->get();

            $data['billing_accounts'] = $user_billing_accounts;

            $data['total'] = $user_billing_accounts->count();

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_billing_accounts_view()
     *
     * @uses Accounts Detailed view
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param
     *
     * @return json response with details
     */

    public function user_billing_accounts_view(Request $request) {

        try {

            $user_billing_accounts = \App\UserBillingAccount::where('id', $request->user_billing_account_id)->CommonResponse()->get();

            $data['billing_accounts'] = $user_billing_accounts;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_billing_accounts_save()
     *
     * @uses To save account details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_billing_accounts_save(Request $request) {

        try {

            DB::beginTransaction();

             // Validation start
            $rules = [
                'user_billing_account_id' => 'nullable|exists:user_billing_accounts,id',
                'account_holder_name' => 'required',
                'account_number' => 'required',
                'ifsc_code' => 'nullable',
                'swift_code' => 'nullable',
                'route_number' => 'nullable',
                'iban_number' => 'nullable',
                'nickname' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $request->request->add(['user_id' => $request->id]);

            if($request->user_billing_account_id) {
                
                $user_billing_account = \App\UserBillingAccount::updateOrCreate(['id' => $request->user_billing_account_id,'account_number' => $request->account_number, 'user_id' => $request->id], $request->all());

            } else {
                
                $user_billing_account = \App\UserBillingAccount::updateOrCreate(['account_number' => $request->account_number, 'user_id' => $request->id], $request->all());

            }

            $user_billing_account->save();

            DB::commit();

            return $this->sendResponse(api_success(112), $success_code = 112, $user_billing_account);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_billing_accounts_delete()
     *
     * @uses To delete account details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_billing_accounts_delete(Request $request) {

        try {

            DB::beginTransaction();

             // Validation start

            $rules = ['user_billing_account_id' => 'required|exists:user_billing_accounts,id,user_id,'.$request->id];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            $user_billing_account = \App\UserBillingAccount::destroy($request->user_billing_account_id);

            DB::commit();

            $data['user_billing_account_id'] = $request->user_billing_account_id;

            return $this->sendResponse(api_success(113), $success_code = 113, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_billing_accounts_default()
     *
     * @uses To make account default
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_billing_accounts_default(Request $request) {

        try {

            DB::beginTransaction();

             // Validation start

            $rules = ['user_billing_account_id' => 'required|exists:user_billing_accounts,id,user_id,'.$request->id];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $old_accounts = \App\UserBillingAccount::where('user_id' , $request->id)->where('is_default', YES)->update(['is_default' => NO]);

            $user_billing_account = \App\UserBillingAccount::where('id' , $request->user_billing_account_id)->update(['is_default' => YES]);

            DB::commit();

            $data['user_billing_account'] = $user_billing_account;

            return $this->sendResponse(api_success(137), $success_code = 137, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method other_profile()
     *
     * @uses Content Creators Profile view
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request - User ID
     *
     * @return json response with user details
     */

    public function other_profile(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\User::OtherResponse()->where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(1002), 1002);
            }

            $user->updated_formatted = common_date($user->updated_at, $this->timezone, 'd M Y');

            $data['user'] = $user;

            $data['payment_info'] = CommonRepo::subscriptions_user_payment_check($user, $request);

            $data['is_favuser'] = \App\FavUser::where('user_id', $request->id)->where('fav_user_id', $user->id)->count() ? YES : NO;

            $data['share_link'] = Setting::get('frontend_url').$request->user_unique_id;

            $data['is_block_user'] = Helper::is_block_user($request->id, $user->user_id);

            $data['total_followers'] = \App\Follower::where('user_id', $request->user_id)->count();

            $data['total_followings'] = \App\Follower::where('follower_id', $request->user_id)->count();

            $data['total_posts'] = \App\Post::where('user_id', $request->user_id)->count();

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method other_profile_posts()
     *
     * @uses Content Creators Posts
     *
     * @created Bhawya N
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function other_profile_posts(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $report_post_ids = report_posts($request->id);

            $base_query = $total_query = \App\Post::with('postFiles')->whereNotIn('posts.id',$report_post_ids)->where('user_id', $user->id);

            if($request->type != POSTS_ALL) {

                $type = $request->type;

                $base_query = $base_query->whereHas('postFiles', function($q) use($type) {
                        $q->where('post_files.file_type', $type);
                    });
            }

            $posts = $base_query->skip($this->skip)->take($this->take)->orderBy('posts.created_at', 'desc')->get();

            $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            $data['posts'] = $posts ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_subscriptions()
     *
     * @uses get subscriptions list for selected user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_subscriptions(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(1002), 1002);
            }

            $user_subscription = \App\UserSubscription::where('user_id', $user->id)->first();

            $data['user_subscription'] = $user_subscription ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_subscriptions_history()
     *
     * @uses get subscriptions list for selected user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_subscriptions_history(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(1002), 1002);
            }

            $user_subscription = \App\UserSubscription::where('user_id', $request->id)->first();

            $data['user_subscription'] = $user_subscription ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_subscriptions_autorenewal()
     *
     * @uses get subscriptions list for selected user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_subscriptions_autorenewal(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(1002), 1002);
            }

            $user_subscription = \App\UserSubscription::where('user_id', $request->id)->first();

            $data['user_subscription'] = $user_subscription ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_subscriptions_payment_by_stripe()
     *
     * @uses pay for subscription using paypal
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function user_subscriptions_payment_by_stripe(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
                'plan_type' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\User::where('users.unique_id', $request->user_unique_id)->first();
            

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription;

            if(!$user_subscription) {
                
                if($request->is_free == YES) {

                    $user_subscription = new \App\UserSubscription;

                    $user_subscription->user_id = $user->id;

                    $user_subscription->save();
                    
                } else {

                    throw new Exception(api_error(155), 155);   
 
                }

            }
           
            $check_user_payment = \App\UserSubscriptionPayment::UserPaid($request->id, $user->id)->first();

            if($check_user_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $request->plan_type == PLAN_TYPE_YEAR ? $user_subscription->yearly_amount : $user_subscription->monthly_amount;

            $request->request->add(['payment_mode' => CARD]);

            $total = $user_pay_amount = $subscription_amount ?: 0.00;

            if($user_pay_amount > 0) {

                $user_card = \App\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120); 

                }
                
                $request->request->add([
                    'total' => $total, 
                    'customer_id' => $user_card->customer_id,
                    'card_token' => $user_card->card_token,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);


                $card_payment_response = PaymentRepo::user_subscriptions_payment_by_stripe($request, $user_subscription)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }

            $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription)->getData();

            if(!$payment_response->success) {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

            DB::commit();

                return $this->sendResponse(api_success(140), 140, $payment_response->data);
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method user_subscriptions_payment_by_wallet()
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

    public function user_subscriptions_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
                'plan_type' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription;

            if(!$user_subscription) {
                throw new Exception(api_error(155), 155);   
            }

            $check_user_payment = \App\UserSubscriptionPayment::UserPaid($request->id, $user->id)->first();

            if($check_user_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $request->plan_type == PLAN_TYPE_YEAR ? $user_subscription->yearly_amount : $user_subscription->monthly_amount;

            // Check the user has enough balance 

            $user_wallet = \App\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if($remaining < $subscription_amount) {
                throw new Exception(api_error(147), 147);    
            }
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $subscription_amount, 
                'user_pay_amount' => $subscription_amount,
                'paid_amount' => $subscription_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'to_user_id' => $user_subscription->user_id,
                'payment_id' => 'WPP-'.rand()
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription)->getData();

                if(!$payment_response->success) {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }

                DB::commit();

                return $this->sendResponse(api_success(140), 140, $payment_response->data ?? []);

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /** 
     * @method lists_index()
     *
     * @uses To display the user details based on user  id
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function lists_index(Request $request) {

        try {

            $user = User::firstWhere('id' , $request->id);

            if(!$user) { 

                throw new Exception(api_error(1002) , 1002);
            }

            $data = [];

            $data['username'] = $user->username;

            $data['name'] = $user->name;

            $data['user_unique_id'] = $user->unique_id;

            $data['user_id'] = $user->user_id;

            $data['total_followers'] = $user->total_followers ?? 0;

            $data['total_followings'] = $user->total_followings ?? 0;

            $data['total_posts'] = $user->total_posts ?? 0;

            $data['total_fav_users'] = $user->total_fav_users ?? 0;

            $data['total_bookmarks'] = $user->total_bookmarks ?? 0;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method payments_index()
     *
     * @uses To display the user details based on user  id
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function payments_index(Request $request) {

        try {

            $user = User::firstWhere('id' , $request->id);

            if(!$user) { 

                throw new Exception(api_error(1002) , 1002);
            }

            $data = [];

            $data['user'] = $user;

            $data['user_withdrawals_min_amount'] = Setting::get('user_withdrawals_min_amount', 10);

            $data['user_withdrawals_min_amount_formatted'] = formatted_amount(Setting::get('user_withdrawals_min_amount', 10));

            $data['user_wallet'] = \App\UserWallet::where('user_id', $request->id)->first();

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method bell_notifications_index()
     *
     * @uses Get the user notifications
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function bell_notifications_index(Request $request) {

        try {

            $base_query = $total_query = \App\BellNotification::where('to_user_id', $request->id)->orderBy('created_at', 'desc');

            $notifications = $base_query->skip($this->skip)->take($this->take)->get() ?? [];

            foreach ($notifications as $key => $notification) {
                $notification->updated_formatted = common_date($notification->updated_at, $this->timezone, 'd M Y');
            }

            $data['notifications'] = $notifications;

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }


    /**
     * @method chat_users_save()
     * 
     * @uses - To save the chat users.
     *
     * @created Ganesh
     *
     * @updated Ganesh
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function chat_users_save(Request $request) {

        try {

            $rules = [
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|exists:users,id',
            ];


            Helper::custom_validator($request->all(), $rules);

            DB::beginTransaction();

            $chat_user = \App\ChatUser::where('from_user_id', $request->from_user_id)->where('to_user_id', $request->to_user_id)->first();

            if($chat_user) {

                // throw new Exception(api_error(162) , 162);
            } else {

                $chat_user = new \App\ChatUser();

                $chat_user->from_user_id = $request->from_user_id;

                $chat_user->to_user_id = $request->to_user_id;
                
                $chat_user->save();
            }

            DB::commit();

            return $this->sendResponse("", "", $chat_user);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }


    /** 
     * @method block_users_save()
     *
     * @uses block the user using user_id
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function block_users_save(Request $request) {

        try {

            // Validation start
            $rules = [
                'user_id' => 'required|exists:users,id',
                'reason' => 'nullable|max:255'
            ];

            Helper::custom_validator($request->all(),$rules, $custom_errors=[]);

            $check_blocked_user = \App\BlockUser::where('block_by', $request->id)->where('blocked_to', $request->user_id)->first();

            // Check the user already blocked 

            if($check_blocked_user) {

                $block_user = $check_blocked_user->delete();

                $code = 156;

            } else {

                $custom_request = new Request();

                $custom_request->request->add(['block_by' => $request->id, 'blocked_to' => $request->user_id,'reason'=>$request->reason]);

                $block_user = \App\BlockUser::updateOrCreate($custom_request->request->all());

                $code = 155;

                // Check the user already following the selected users

                $follower = \App\Follower::where('user_id', $request->user_id)->where('follower_id', $request->id)->delete();

                $follower = \App\Follower::where('user_id', $request->id)->where('follower_id', $request->user_id)->delete();

                $user_subscription_payment = \App\UserSubscriptionPayment::where('to_user_id', $request->user_id)->where('from_user_id', $request->id)->where('is_current_subscription', YES)->first();

                if($user_subscription_payment) {

                    $user_subscription_payment->is_current_subscription = NO;

                    $user_subscription_payment->cancel_reason = 'unfollowed';

                    $user_subscription_payment->save();
                }

            }

            DB::commit(); 

            $data = [];

            $data['total_followers'] = \App\Follower::where('user_id', $request->id)->count();

            $data['total_followings'] = \App\Follower::where('follower_id', $request->id)->count();

            return $this->sendResponse(api_success($code), $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }


    /**
     * @method block_users()
     * 
     * @uses list of blocked users
     *
     * @created Ganesh 
     *
     * @updated Ganesh
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function block_users(Request $request) {

        try {

            $base_query = $total_query = \App\BlockUser::where('block_by', $request->id)->Approved()->orderBy('block_users.created_at', 'DESC');

            $block_users = $base_query->skip($this->skip)->take($this->take)->get();

            $data['block_users'] = $block_users ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }



     /** 
     * @method user_subscriptions_payment_by_paypal()
     *
     * @uses pay for subscription using paypal
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function user_subscriptions_payment_by_paypal(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'payment_id' => 'required',
                'user_unique_id' => 'required|exists:users,unique_id',
                'plan_type' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\User::where('users.unique_id', $request->user_unique_id)->first();
            
            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription;

            if(!$user_subscription) {
                
                if($request->is_free == YES) {

                    $user_subscription = new \App\UserSubscription;

                    $user_subscription->user_id = $user->id;

                    $user_subscription->save();
                    
                } else {

                    throw new Exception(api_error(155), 155);   
 
                }

            }
           
            $check_user_payment = \App\UserSubscriptionPayment::UserPaid($request->id, $user->id)->first();

            if($check_user_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $request->plan_type == PLAN_TYPE_YEAR ? $user_subscription->yearly_amount : $user_subscription->monthly_amount;

            $user_pay_amount = $subscription_amount ?: 0.00;
              
            $request->request->add(['payment_mode' => PAYPAL,'paid_amount'=>$user_pay_amount]);

            $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription)->getData();

            if(!$payment_response->success) {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

            DB::commit();

            return $this->sendResponse(api_success(140), 140, $payment_response->data);
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }



}
