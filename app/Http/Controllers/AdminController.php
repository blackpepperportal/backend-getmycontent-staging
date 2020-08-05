<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Admin, App\User;

use App\Settings, App\StaticPage;

use App\Jobs\SendEmailJob;

use Carbon\Carbon;

class AdminController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

        $this->middleware('auth:admin');

        $this->skip = $request->skip ?: 0;
       
        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    /**
     * @method index()
     *
     * @uses Show the application dashboard.
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function index() {
        
        $data = new \stdClass;

        $data->total_users = User::count();

        return view('admin.dashboard')
                    ->with('page' , 'dashboard')
                    ->with('data', $data);
    
    }

     /**
     * @method users_index()
     *
     * @uses To list out users details 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function users_index(Request $request) {

        $base_query = User::orderBy('created_at','desc');

        if($request->search_key) {

            $base_query = $base_query
                    ->orWhere('name','LIKE','%'.$request->search_key.'%')
                    ->orWhere('email','LIKE','%'.$request->search_key.'%')
                    ->orWhere('mobile','LIKE','%'.$request->search_key.'%');
        }

        if($request->status) {

            switch ($request->status) {

                case SORT_BY_APPROVED:
                    $base_query = $base_query->where('status',APPROVED);
                    break;

                case SORT_BY_DECLINED:
                    $base_query = $base_query->where('status',DECLINED);
                    break;

                case SORT_BY_EMAIL_VERIFIED:
                    $base_query = $base_query->where('is_verified',USER_EMAIL_VERIFIED);
                    break;
                
                default:
                    $base_query = $base_query->where('is_verified',USER_EMAIL_NOT_VERIFIED);
                    break;
            }
        }

        $users = $base_query->paginate(10);

        return view('admin.users.index')
                    ->with('main_page','users-crud')
                    ->with('page','users')
                    ->with('sub_page' , 'users-view')
                    ->with('users' , $users);
    }

    /**
     * @method users_create()
     *
     * @uses To create user details
     *
     * @created  Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function users_create() {

        $user_details = new User;

        return view('admin.users.create')
                    ->with('main_page','users-crud')
                    ->with('page' , 'users')
                    ->with('sub_page','users-create')
                    ->with('user_details', $user_details);           
    }

    /**
     * @method users_edit()
     *
     * @uses To display and update user details based on the user id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return redirect view page 
     *
     */
    public function users_edit(Request $request) {

        try {

            $user_details = User::find($request->user_id);

            if(!$user_details) { 

                throw new Exception(tr('user_not_found'), 101);
            }

            return view('admin.users.edit')
                    ->with('main_page','users-crud')
                    ->with('page' , 'users')
                    ->with('sub_page','users-view')
                    ->with('user_details' , $user_details); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method users_save()
     *
     * @uses To save the users details of new/existing user object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - User Form Data
     *
     * @return success message
     *
     */
    public function users_save(Request $request) {
        
        try {

            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'email' => $request->user_id ? 'required|email|max:191|unique:users,email,'.$request->user_id.',id' : 'required|email|max:191|unique:users,email,NULL,id',
                'password' => $request->user_id ? "" : 'required|min:6',
                'mobile' => $request->mobile ? 'digits_between:6,13' : '',
                'picture' => 'mimes:jpg,png,jpeg',
                'user_id' => 'exists:users,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_details = $request->user_id ? User::find($request->user_id) : new User;

            $is_new_user = NO;

            if($user_details->id) {

                $message = tr('user_updated_success'); 

            } else {

                $is_new_user = YES;

                $user_details->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('user_created_success');

                $user_details->email_verified_at = date('Y-m-d H:i:s');

                $user_details->picture = asset('placeholder.jpeg');

                $user_details->is_verified = USER_EMAIL_VERIFIED;

                $user_details->token = Helper::generate_token();

                $user_details->token_expiry = Helper::generate_token_expiry();

            }

            $user_details->name = $request->name ?: $user_details->name;

            $user_details->email = $request->email ?: $user_details->email;

            $user_details->mobile = $request->mobile ?: '';

            $user_details->login_by = $request->login_by ?: 'manual';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_id) {

                    Helper::storage_delete_file($user_details->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $user_details->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($user_details->save()) {

                if($is_new_user == YES) {

                    /**
                     * @todo Welcome mail notification
                     */

                    $user_details->is_verified = USER_EMAIL_VERIFIED;

                    $user_details->save();

                }

                DB::commit(); 

                return redirect(route('admin.users.view', ['user_id' => $user_details->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('user_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method users_view()
     *
     * @uses view the users details based on users id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return View page
     *
     */
    public function users_view(Request $request) {
       
        try {
      
            $user_details = User::find($request->user_id);

            if(!$user_details) { 

                throw new Exception(tr('user_not_found'), 101);                
            }

            return view('admin.users.view')
                        ->with('main_page','users-crud')
                        ->with('page', 'users') 
                        ->with('sub_page','users-view') 
                        ->with('user_details' , $user_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method users_delete()
     *
     * @uses delete the user details based on user id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - User Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function users_delete(Request $request) {

        try {

            DB::begintransaction();

            $user_details = User::find($request->user_id);
            
            if(!$user_details) {

                throw new Exception(tr('user_not_found'), 101);                
            }

            if($user_details->delete()) {

                DB::commit();

                return redirect()->route('admin.users.index')->with('flash_success',tr('user_deleted_success'));   

            } 
            
            throw new Exception(tr('user_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method users_status
     *
     * @uses To update user status as DECLINED/APPROVED based on users id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return response success/failure message
     *
     **/
    public function users_status(Request $request) {

        try {

            DB::beginTransaction();

            $user_details = User::find($request->user_id);

            if(!$user_details) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user_details->status = $user_details->status ? DECLINED : APPROVED ;

            if($user_details->save()) {

                DB::commit();

                $message = $user_details->status ? tr('user_approve_success') : tr('user_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method users_verify_status()
     *
     * @uses verify the user
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - User Id
     *
     * @return redirect back page with status of the user verification
     */
    public function users_verify_status(Request $request) {

        try {

            DB::beginTransaction();

            $user_details = User::find($request->user_id);

            if(!$user_details) {

                throw new Exception(tr('user_details_not_found'), 101);
                
            }

            $user_details->is_verified = $user_details->is_verified ? USER_EMAIL_NOT_VERIFIED : USER_EMAIL_VERIFIED;

            if($user_details->save()) {

                DB::commit();

                $message = $user_details->is_verified ? tr('user_verify_success') : tr('user_unverify_success');

                return redirect()->route('admin.users.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }
    
    }

     /**
     * @method static_pages_index()
     *
     * @uses Used to list the static pages
     *
     * @created vithya
     *
     * @updated vithya  
     *
     * @param -
     *
     * @return List of pages   
     */

    public function static_pages_index() {

        $static_pages = StaticPage::orderBy('updated_at' , 'desc')->paginate(10);

        return view('admin.static_pages.index')
                    ->with('page','static_pages')
                    ->with('sub_page',"static_pages-view")
                    ->with('static_pages',$static_pages);
    
    }

    /**
     * @method static_pages_create()
     *
     * @uses To create static_page details
     *
     * @created Akshata
     *
     * @updated    
     *
     * @param
     *
     * @return view page   
     *
     */
    public function static_pages_create() {

        $static_keys = ['about' , 'contact' , 'privacy' , 'terms' , 'help' , 'faq' , 'refund', 'cancellation'];

        foreach ($static_keys as $key => $static_key) {

            // Check the record exists

            $check_page = StaticPage::where('type', $static_key)->first();

            if($check_page) {
                unset($static_keys[$key]);
            }
        }

        $section_types = static_page_footers(0, $is_list = YES);

        $static_keys[] = 'others';

        $static_page_details = new StaticPage;

        return view('admin.static_pages.create')
                ->with('page','static_pages')
                ->with('sub_page',"static_pages-create")
                ->with('static_keys', $static_keys)
                ->with('static_page_details',$static_page_details)
                ->with('section_types',$section_types);
   
    }

    /**
     * @method static_pages_edit()
     *
     * @uses To display and update static_page details based on the static_page id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - static_page Id
     * 
     * @return redirect view page 
     *
     */
    public function static_pages_edit(Request $request) {

        try {

            $static_page_details = StaticPage::find($request->static_page_id);

            if(!$static_page_details) {

                throw new Exception(tr('static_page_not_found'), 101);
            }

            $static_keys = ['about' , 'contact' , 'privacy' , 'terms' , 'help' , 'faq' , 'refund', 'cancellation'];

            foreach ($static_keys as $key => $static_key) {

                // Check the record exists

                $check_page = StaticPage::where('type', $static_key)->first();

                if($check_page) {
                    unset($static_keys[$key]);
                }
            }

            $section_types = static_page_footers(0, $is_list = YES);

            $static_keys[] = 'others';

            $static_keys[] = $static_page_details->type;

            return view('admin.static_pages.edit')
                    ->with('page' , 'static_pages')
                    ->with('sub_page','static_pages-view')
                    ->with('static_keys' , array_unique($static_keys))
                    ->with('static_page_details' , $static_page_details)
                    ->with('section_types',$section_types);
            
        } catch(Exception $e) {

            $error = $e->getMessage();

            return redirect()->route('admin.static_pages.index')->with('flash_error' , $error);

        }
    }

    /**
     * @method static_pages_save()
     *
     * @uses Used to create/update the page details 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param
     *
     * @return index page    
     *
     */
    public function static_pages_save(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'title' => 'required|max:191',
                'description' => 'required',
                'type' => !$request->static_page_id ? 'required' : ""
            ]; 
            
            Helper::custom_validator($request->all(), $rules);

            if($request->static_page_id != '') {

                $static_page_details = StaticPage::find($request->static_page_id);

                $message = tr('static_page_updated_success');                    

            } else {

                $check_page = "";

                // Check the staic page already exists

                if($request->type != 'others') {

                    $check_page = StaticPage::where('type',$request->type)->first();

                    if($check_page) {

                        return back()->with('flash_error',tr('static_page_already_alert'));
                    }

                }

                $message = tr('static_page_created_success');

                $static_page_details = new StaticPage;

                $static_page_details->status = APPROVED;

            }

            $static_page_details->title = $request->title ?: $static_page_details->title;

            $static_page_details->description = $request->description ?: $static_page_details->description;

            $static_page_details->type = $request->type ?: $static_page_details->type;

            $static_page_details->section_type = $request->section_type ?: $static_page_details->section_type;

            if($static_page_details->save()) {

                DB::commit();

                Helper::settings_generate_json();
                
                return redirect()->route('admin.static_pages.view', ['static_page_id' => $static_page_details->id] )->with('flash_success', $message);

            } 

            throw new Exception(tr('static_page_save_failed'), 101);
                      
        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return back()->withInput()->with('flash_error', $error);

        }
    
    }

    /**
     * @method static_pages_delete()
     *
     * Used to view file of the create the static page 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param -
     *
     * @return view page   
     */

    public function static_pages_delete(Request $request) {

        try {

            DB::beginTransaction();

            $static_page_details = StaticPage::find($request->static_page_id);

            if(!$static_page_details) {

                throw new Exception(tr('static_page_not_found'), 101);
                
            }

            if($static_page_details->delete()) {

                DB::commit();

                return redirect()->route('admin.static_pages.index')->with('flash_success',tr('static_page_deleted_success')); 

            } 

            throw new Exception(tr('static_page_error'));

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return redirect()->route('admin.static_pages.index')->with('flash_error', $error);

        }
    
    }

    /**
     * @method static_pages_view()
     *
     * @uses view the static_pages details based on static_pages id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - static_page Id
     * 
     * @return View page
     *
     */
    public function static_pages_view(Request $request) {

        $static_page_details = StaticPage::find($request->static_page_id);

        if(!$static_page_details) {
           
            return redirect()->route('admin.static_pages.index')->with('flash_error',tr('static_page_not_found'));

        }

        return view('admin.static_pages.view')
                    ->with('page', 'static_pages')
                    ->with('sub_page','static_pages-view')
                    ->with('static_page_details' , $static_page_details);
    }

    /**
     * @method static_pages_status_change()
     *
     * @uses To update static_page status as DECLINED/APPROVED based on static_page id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param - integer static_page_id
     *
     * @return view page 
     */

    public function static_pages_status_change(Request $request) {

        try {

            DB::beginTransaction();

            $static_page_details = StaticPage::find($request->static_page_id);

            if(!$static_page_details) {

                throw new Exception(tr('static_page_not_found'), 101);
                
            }

            $static_page_details->status = $static_page_details->status == DECLINED ? APPROVED : DECLINED;

            $static_page_details->save();

            DB::commit();

            $message = $static_page_details->status == DECLINED ? tr('static_page_decline_success') : tr('static_page_approve_success');

            return redirect()->back()->with('flash_success', $message);

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return redirect()->back()->with('flash_error', $error);

        }

    }


    /**
     * @method settings()
     *
     * @uses  Used to display the setting page
     *
     * @created Akshata
     *
     * @updated
     *
     * @param 
     *
     * @return view page 
     */

    public function settings() {

        $env_values = EnvEditorHelper::getEnvValues();

        return view('admin.settings.settings')
                ->with('env_values',$env_values)
                ->with('page' , 'settings');
    }
    
    /**
     * @method settings_save()
     * 
     * @uses to update settings details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param (request) setting details
     *
     * @return success/error message
     */
    public function settings_save(Request $request) {
      
        try {
            
            DB::beginTransaction();
            
            $rules =  
                [
                    'site_logo' => 'mimes:jpeg,jpg,bmp,png',
                    'site_icon' => 'mimes:jpeg,jpg,bmp,png',
                ];

            $custom_errors = 
                [
                    'mimes' => tr('image_error')
                ];

            Helper::custom_validator($request->all(),$rules,$custom_errors);

            foreach( $request->toArray() as $key => $value) {

                if($key != '_token') {

                    $check_settings = Settings::where('key' ,'=', $key)->count();

                    if( $check_settings == 0 ) {

                        throw new Exception( $key.tr('settings_key_not_found'), 101);
                    }
                    
                    if( $request->hasFile($key) ) {
                                            
                        $file = Settings::where('key' ,'=', $key)->first();
                       
                        Helper::storage_delete_file($file->value, FILE_PATH_SITE);

                        $file_path = Helper::storage_upload_file($request->file($key) , FILE_PATH_SITE);    

                        $result = Settings::where('key' ,'=', $key)->update(['value' => $file_path]); 

                        if( $result == TRUE ) {
                     
                            DB::commit();
                   
                        } else {

                            throw new Exception(tr('settings_save_error'), 101);
                        } 
                   
                    } else {

                        if(isset($value)) {

                            $result = Settings::where('key' ,'=', $key)->update(['value' => $value]);

                        } else {

                            $result = Settings::where('key' ,'=', $key)->update(['value' => '']);
                        } 
                        
                        if( $result == TRUE ) {
                         
                            DB::commit();
                       
                        } else {

                            throw new Exception(tr('settings_save_error'), 101);
                        } 

                    }  
 
                }
            }

            Helper::settings_generate_json();

            return back()->with('flash_success', tr('settings_update_success'));
            
        } catch (Exception $e) {

            DB::rollback();

            return back()->with('flash_error', $e->getMessage());
        
        }
    }

    /**
     * @method env_settings_save()
     *
     * @uses To update the email details for .env file
     *
     * @created Akshata
     *
     * @updated
     *
     * @param Form data
     *
     * @return view page
     */

    public function env_settings_save(Request $request) {

        try {

            $env_values = EnvEditorHelper::getEnvValues();

            $env_settings = ['MAIL_DRIVER' , 'MAIL_HOST' , 'MAIL_PORT' , 'MAIL_USERNAME' , 'MAIL_PASSWORD' , 'MAIL_ENCRYPTION' , 'MAILGUN_DOMAIN' , 'MAILGUN_SECRET' , 'FCM_SERVER_KEY', 'FCM_SENDER_ID' , 'FCM_PROTOCOL'];

            if($env_values) {

                foreach ($env_values as $key => $data) {

                    if($request->$key) { 

                        \Enveditor::set($key, $request->$key);

                    }
                }
            }

            $message = tr('settings_update_success');

            return redirect()->route('clear-cache')->with('flash_success', $message);  

        } catch(Exception $e) {

            return back()->withInput()->with('flash_error' , $e->getMessage());

        }  

    }

    /**
     * @method profile()
     *
     * @uses  Used to display the logged in admin details
     *
     * @created Akshata
     *
     * @updated
     *
     * @param 
     *
     * @return view page 
     */

    public function profile() {

        return view('admin.account.profile')
                ->with('page', 'profile');
    }


    /**
     * @method profile_save()
     *
     * @uses To update the admin details
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */

    public function profile_save(Request $request) {

        try {

            DB::beginTransaction();

            $rules = 
                [
                    'name' => 'max:191',
                    'email' => $request->admin_id ? 'email|max:191|unique:admins,email,'.$request->admin_id : 'email|max:191|unique:admins,email,NULL',
                    'admin_id' => 'required|exists:admins,id',
                    'picture' => 'mimes:jpeg,jpg,png'
                ];
            
            Helper::custom_validator($request->all(),$rules);
            
            $admin_details = Admin::find($request->admin_id);

            if(!$admin_details) {

                Auth::guard('admin')->logout();

                throw new Exception(tr('admin_details_not_found'), 101);
            }
        
            $admin_details->name = $request->name ?: $admin_details->name;

            $admin_details->email = $request->email ?: $admin_details->email;

            if($request->hasFile('picture') ) {
                
                Helper::storage_delete_file($admin_details->picture, PROFILE_PATH_ADMIN); 
                
                $admin_details->picture = Helper::storage_upload_file($request->file('picture'), PROFILE_PATH_ADMIN);
            }
            
            $admin_details->remember_token = Helper::generate_token();

            $admin_details->save();

            DB::commit();

            return redirect()->route('admin.profile')->with('flash_success', tr('admin_profile_success'));


        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error' , $e->getMessage());

        }    
    
    }

    /**
     * @method change_password()
     *
     * @uses To change the admin password
     *
     * @created Akshata
     *
     * @updated
     *
     * @param 
     *
     * @return view page 
     */

    public function change_password(Request $request) {

        try {

            DB::begintransaction();

            $rules = 
            [              
                'password' => 'required|confirmed|min:6',
                'old_password' => 'required',
            ];
            
            Helper::custom_validator($request->all(),$rules);

            $admin_details = Admin::find(Auth::guard('admin')->user()->id);

            if(!$admin_details) {

                Auth::guard('admin')->logout();
                              
                throw new Exception(tr('admin_details_not_found'), 101);

            }

            if(Hash::check($request->old_password,$admin_details->password)) {

                $admin_details->password = Hash::make($request->password);

                $admin_details->save();

                DB::commit();

                Auth::guard('admin')->logout();

                return redirect()->route('admin.login')->with('flash_success', tr('password_change_success'));
                
            } else {

                throw new Exception(tr('password_mismatch'));
            }

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error' , $e->getMessage());

        }    
    
    }
    
    /**
     * @method admin_control()
     *
     * @uses 
     *
     * @created Akshata
     *
     * @updated
     *
     * @param 
     *
     * @return view page 
     */
    public function admin_control() {
           
        return view('admin.settings.control')->with('page', tr('admin_control'));
        
    }
}

