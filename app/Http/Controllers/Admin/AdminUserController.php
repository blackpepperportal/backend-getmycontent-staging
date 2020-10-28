<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

use Excel;

use App\Exports\UsersExport;

class AdminUserController extends Controller
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

        $base_query = \App\User::orderBy('created_at','desc');

        if($request->search_key) {

            $base_query = $base_query
                    ->where('users.name','LIKE','%'.$request->search_key.'%')
                    ->orWhere('users.email','LIKE','%'.$request->search_key.'%')
                    ->orWhere('users.mobile','LIKE','%'.$request->search_key.'%');
        }

        if($request->status) {

            switch ($request->status) {

                case SORT_BY_APPROVED:
                    $base_query = $base_query->where('users.status', USER_APPROVED);
                    break;

                case SORT_BY_DECLINED:
                    $base_query = $base_query->where('users.status', USER_DECLINED);
                    break;

                case SORT_BY_EMAIL_VERIFIED:
                    $base_query = $base_query->where('users.is_email_verified',USER_EMAIL_VERIFIED);
                    break;
                
                default:
                    $base_query = $base_query->where('users.is_email_verified',USER_EMAIL_NOT_VERIFIED);
                    break;
            }
        }

        $page = 'users'; $sub_page = 'users-view';

        if($request->has('account_type')) {

            $page = $request->account_type == USER_FREE_ACCOUNT ? 'users-free' : 'users-premium'; $sub_page = '';

            $base_query = $base_query->where('users.user_account_type', $request->account_type);

        } 

        $users = $base_query->paginate(10);

        return view('admin.users.index')
                    ->with('page', $page)
                    ->with('sub_page', $sub_page)
                    ->with('users', $users);
    
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

        $user_details = new \App\User;


        return view('admin.users.create')
                    ->with('page', 'users')
                    ->with('sub_page','users-create')
                    ->with('user_details', $user_details);           
   
    }

    public function users_excel() {

        try{
            $file_format = '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;


            return Excel::download(new UsersExport, $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error' , $e->getMessage());

        }

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

            $user_details = \App\User::find($request->user_id);

            if(!$user_details) { 

                throw new Exception(tr('user_not_found'), 101);
            }

            return view('admin.users.edit')
                    ->with('page', 'users')
                    ->with('sub_page', 'users-view')
                    ->with('user_details', $user_details); 
            
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
                'first_name' => 'required|max:191',
                'last_name' => 'required|max:191',
                // 'email' => 'email|unique:users,email,'.$request->id.'|max:255',
                'username' => 'nullable|unique:users,username,'.$request->user_id.'|max:255',
                'email' => $request->user_id ? 'required|email|max:191|unique:users,email,'.$request->user_id.',id' : 'required|email|max:191|unique:users,email,NULL,id',
                'password' => $request->user_id ? "" : 'required|min:6|confirmed',
                'mobile' => $request->mobile ? 'digits_between:6,13' : '',
                'picture' => 'mimes:jpg,png,jpeg',
                'user_id' => 'exists:users,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user = $request->user_id ? \App\User::find($request->user_id) : new \App\User;

            $is_new_user = NO;

            if($user->id) {

                $message = tr('user_updated_success'); 

            } else {

                $is_new_user = YES;

                $user->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('user_created_success');

                $user->email_verified_at = date('Y-m-d H:i:s');

                $user->picture = asset('placeholder.jpeg');

                $user->is_email_verified = USER_EMAIL_VERIFIED;

                $user->token = Helper::generate_token();

                $user->token_expiry = Helper::generate_token_expiry();

            }

            
            $user->first_name = $request->first_name;

            $user->last_name = $request->last_name;

            $user->email = $request->email;

            $user->mobile = $request->mobile ?: "";

            $user->gender = $request->gender ?: "male";

            $user->website = $request->website ?: "";

            $user->amazon_wishlist = $request->amazon_wishlist ?: "";

            $user->login_by = $request->login_by ?: 'manual';

            $username = $request->username ?: $user->username;

            $user->unique_id = $user->username = routefreestring(strtolower($username));
            
            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_id) {

                    Helper::storage_delete_file($user->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $user->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($user->save()) {

                if($is_new_user == YES) {

                    /**
                     * @todo Welcome mail notification
                     */

                    $email_data['subject'] = tr('user_welcome_email' , Setting::get('site_name'));

                    $email_data['email']  = $user->email;

                    $email_data['name'] = $user->first_name;

                    $email_data['page'] = "emails.users.welcome";

                    $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    $user->is_email_verified = USER_EMAIL_VERIFIED;

                    $user->save();

                }

                DB::commit(); 

                return redirect(route('admin.users.view', ['user_id' => $user->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('user_save_failed'));
            
        } 
        catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method users_view()
     *
     * @uses Display the specified user details based on user_id
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
      
            $user_details = \App\User::find($request->user_id);
            

            if(!$user_details) { 

                throw new Exception(tr('user_not_found'), 101);                
            }

            return view('admin.users.view')
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

            $user_details = \App\User::find($request->user_id);
            
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

            $user_details = \App\User::find($request->user_id);

            if(!$user_details) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user_details->status = $user_details->status ? DECLINED : APPROVED ;

            if($user_details->save()) {

                if($user_details->status == DECLINED) {

                    $email_data['subject'] = tr('user_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('user_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');

                }

                $email_data['email']  = $user_details->email;

                $email_data['name']  = $user_details->name;

                $email_data['page'] = "emails.users.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

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

            $user_details = \App\User::find($request->user_id);

            if(!$user_details) {

                throw new Exception(tr('user_details_not_found'), 101);
                
            }

            $user_details->is_email_verified = $user_details->is_email_verified ? USER_EMAIL_NOT_VERIFIED : USER_EMAIL_VERIFIED;

            if($user_details->save()) {

                DB::commit();

                $message = $user_details->is_email_verified ? tr('user_verify_success') : tr('user_unverify_success');

                return redirect()->route('admin.users.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }
    
    }

     /**
     * @method user_followers()
     *
     * @uses This is to display the all followers of specified content creator
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - follower Id
     *
     * @return view page
     */
     public function user_followers(Request $request) {

        $user_followers = \App\Follower::where('follower_id',$request->follower_id)->paginate($this->take);
        
        return view('admin.users.followers')
                ->with('page','users')
                ->with('sub_page','users-view')
                ->with('user_followers',$user_followers);
     }

     /**
     * @method user_following()
     *
     * @uses This is to display the all followers of specified 
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - follower Id
     *
     * @return view page
     */
     public function user_following(Request $request) {

        $user_followings = \App\Follower::where('user_id',$request->user_id)->paginate($this->take);
        $users_name = \App\User::where('id', $request->user_id)->first()->name;

        return view('admin.users.following')
                ->with('page','users')
                ->with('sub_page','users-view')
                ->with('user_followings',$user_followings)
                ->with('users_name',$users_name);
       
     }
}
