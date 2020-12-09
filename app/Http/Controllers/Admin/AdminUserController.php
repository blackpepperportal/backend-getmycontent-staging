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

                case SORT_BY_DOCUMENT_VERIFIED:

                    $base_query =  $base_query->whereHas('userDocuments', function($q) use ($request) {
                                    return $q->where('user_documents.is_verified',USER_DOCUMENT_VERIFIED);
                                   });
                    break;
                case SORT_BY_DOCUMENT_APPROVED:

                    $base_query = $base_query->where('users.is_document_verified',USER_DOCUMENT_APPROVED);
                    break;
                
                default:
                    $base_query = $base_query->where('users.is_email_verified',USER_EMAIL_NOT_VERIFIED);
                    break;
            }
        }

        $page = 'users'; $sub_page = 'users-view';

        $title = tr('view_users');

        if($request->has('account_type')) {

            $page = $request->account_type == USER_FREE_ACCOUNT ? 'users-free' : 'users-premium'; $sub_page = '';

            $title = $request->account_type == USER_FREE_ACCOUNT ? tr('free_users') : tr('premium_users');

            $base_query = $base_query->where('users.user_account_type', $request->account_type);

        } 
      
        $users = $base_query->paginate($this->take);

        return view('admin.users.index')
                    ->with('page', $page)
                    ->with('sub_page', $sub_page)
                    ->with('title', $title)
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

        $user = new \App\User;


        return view('admin.users.create')
                    ->with('page', 'users')
                    ->with('sub_page','users-create')
                    ->with('user', $user);           
   
    }

    public function users_excel(Request $request) {

        try{
            $file_format = '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new UsersExport($request), $filename);

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

            $user = \App\User::find($request->user_id);

            if(!$user) { 

                throw new Exception(tr('user_not_found'), 101);
            }

            return view('admin.users.edit')
                    ->with('page', 'users')
                    ->with('sub_page', 'users-view')
                    ->with('user', $user); 
            
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
                'user_id' => 'exists:users,id|nullable',
                'cover' => 'nullable|mimes:jpeg,bmp,png,jpg',
                'gender' => 'nullable|in:male,female,others',
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

            $user->user_account_type = $request->user_account_type;

            $user->unique_id = $user->username = routefreestring(strtolower($username));
            
            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_id) {

                    Helper::storage_delete_file($user->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $user->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($request->hasFile('cover') != "") {

                Helper::storage_delete_file($user->cover, COMMON_FILE_PATH); // Delete the old pic

                $user->cover = Helper::storage_upload_file($request->file('cover'), COMMON_FILE_PATH);
            
            }

            if($user->save()) {

                if($request->monthly_amount || $request->yearly_amount) {


                    $user_subscription = \App\UserSubscription::where('user_id', $user->id)->first() ?? new \App\UserSubscription;

                    $user_subscription->user_id = $user->id;

                    $user_subscription->monthly_amount = $request->monthly_amount ?: ($user_subscription->monthly_amount ?: 0.00);

                    $user_subscription->yearly_amount = $request->yearly_amount ?: ($user_subscription->yearly_amount ?: 0.00);

                    $user_subscription->save();

                    DB::commit();

                }


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

                    $user->user_account_type = $request->user_account_type;

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
     * @method user_upgrade_account()
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
    public function user_upgrade_account(Request $request) {

        try {

            DB::beginTransaction();

              $rules = [
                'user_id' => 'required',
                'monthly_amount' => 'required_without:yearly_amount',
                'yearly_amount' => 'required_without:monthly_amount',
            ];


            Helper::custom_validator($request->all(),$rules);

            $user = \App\User::find($request->user_id);


            if(!$user) { 

                throw new Exception(tr('user_not_found'), 101);                
            }

            $user->user_account_type = USER_PREMIUM_ACCOUNT;

            $user->is_document_verified = USER_DOCUMENT_APPROVED;
           
            if($user->save()) {

                if($request->is_billing_account) {

                    $user_billing_account = new \App\UserBillingAccount;

                    $user_billing_account->user_id = $request->user_id;

                    $user_billing_account->nickname = $request->nickname;

                    $user_billing_account->account_holder_name = $request->account_holder_name;

                    $user_billing_account->account_number = $request->account_number;

                    $user_billing_account->ifsc_code = $request->ifsc_code;

                    $user_billing_account->swift_code = $request->swift_code;

                    $user_billing_account->bank_name = $request->bank_name;

                    $user_billing_account->save();
                }

                if($request->monthly_amount || $request->yearly_amount) {


                   $user_subscription =  \App\UserSubscription::find($request->subscription_id) ?? new \App\UserSubscription ;

                    $user_subscription->user_id = $user->id;

                    $user_subscription->monthly_amount = $request->monthly_amount ??  0.00;

                    $user_subscription->yearly_amount = $request->yearly_amount ?? 0.00;

                    $user_subscription->save();

                    DB::commit();

                }

                $email_data['subject'] = tr('user_account_upgrade').' '.Setting::get('site_name');

                $email_data['email']  = $user->email ?? "-";

                $email_data['name']  = $user->name ?? "-";

                $email_data['page'] = "emails.users.account-upgrade";

                $email_data['message'] = tr('account_upgrade_message', $user->name ?? ''); 

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));


                DB::commit(); 

                return redirect()->back()->with('flash_success',tr('user_upgrade_account',$user->name));

            } 

            throw new Exception(tr('user_upgrade_account_failed'));
            
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
      
            $user = \App\User::find($request->user_id);
            

            if(!$user) { 

                throw new Exception(tr('user_not_found'), 101);                
            }

            return view('admin.users.view')
                        ->with('page', 'users') 
                        ->with('sub_page','users-view') 
                        ->with('user' , $user);
            
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

            $user = \App\User::find($request->user_id);
            
            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);                
            }

            if($user->delete()) {

                DB::commit();

                return redirect()->route('admin.users.index',['page'=>$request->page])->with('flash_success',tr('user_deleted_success'));   

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

            $user = \App\User::find($request->user_id);

            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user->status = $user->status ? DECLINED : APPROVED ;

            if($user->save()) {

                if($user->status == DECLINED) {

                    $email_data['subject'] = tr('user_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('user_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');

                }

                $email_data['email']  = $user->email;

                $email_data['name']  = $user->name;

                $email_data['page'] = "emails.users.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                $message = $user->status ? tr('user_approve_success') : tr('user_decline_success');

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

            $user = \App\User::find($request->user_id);

            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user->is_email_verified = $user->is_email_verified ? USER_EMAIL_NOT_VERIFIED : USER_EMAIL_VERIFIED;

            if($user->save()) {

                $status_message = $user->is_email_verified ? tr('approved'):tr('declined');

                $email_data['subject'] = tr('user_email_verification').' '.Setting::get('site_name');

                $email_data['email']  = $user->email ?? "-";

                $email_data['name']  = $user->name ?? "-";

                $email_data['page'] = "emails.users.email-verify";

                $email_data['message'] = tr('email_verify_message', $status_message); 

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                $message = $user->is_email_verified ? tr('user_verify_success') : tr('user_unverify_success');

                return redirect()->route('admin.users.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }
    
    }


     /**
     * @method users_bulk_action()
     * 
     * @uses To delete,approve,decline multiple users
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     *
     * @return success/failure message
     */
    public function users_bulk_action(Request $request) {

        try {
            
            $action_name = $request->action_name ;

            $user_ids = explode(',', $request->selected_users);

            if (!$user_ids && !$action_name) {

                throw new Exception(tr('user_action_is_empty'));

            }

            DB::beginTransaction();

            if($action_name == 'bulk_delete'){

                $user = \App\User::whereIn('id', $user_ids)->delete();

                if ($user) {

                    DB::commit();

                    return redirect()->back()->with('flash_success',tr('admin_users_delete_success'));

                }

                throw new Exception(tr('user_delete_failed'));

            }elseif($action_name == 'bulk_approve'){

                $user =  \App\User::whereIn('id', $user_ids)->update(['status' => USER_APPROVED]);

                if ($user) {

                    DB::commit();

                    return back()->with('flash_success',tr('admin_users_approve_success'))->with('bulk_action','true');
                }

                throw new Exception(tr('users_approve_failed'));  

            }elseif($action_name == 'bulk_decline'){
                
                $user =  \App\User::whereIn('id', $user_ids)->update(['status' => USER_DECLINED]);

                if ($user) {
                    
                    DB::commit();

                    return back()->with('flash_success',tr('admin_users_decline_success'))->with('bulk_action','true');
                }

                throw new Exception(tr('users_decline_failed')); 
            }

        }catch( Exception $e) {

            DB::rollback();

            return redirect()->back()->with('flash_error',$e->getMessage());
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

      try {

        $user = \App\User::find($request->follower_id);

        if(!$user) {

            throw new Exception(tr('user_not_found'));
        }

        $user_followers = \App\Follower::where('follower_id',$request->follower_id)->paginate($this->take);

        return view('admin.users.followers')
                ->with('page', 'users')
                ->with('sub_page', 'users-view')
                ->with('user_followers', $user_followers)
                ->with('user', $user);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());

        }
     }

    /**
     * @method user_followings()
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
     public function user_followings(Request $request) {
      
      try{

        $user = \App\User::find($request->user_id);

        if(!$user) {

            throw new Exception(tr('user_not_found'));

        }

        $followings = \App\Follower::where('user_id', $request->user_id)->paginate($this->take);

        return view('admin.users.followings')
                ->with('page','users')
                ->with('sub_page','users-view')
                ->with('followings', $followings)
                ->with('user', $user);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());

        }
       
    }

    /**
     * @method user_documents_index()
     *
     * @uses Lists all users documents 
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - Stardom document Id
     *
     * @return view page
     */
    public function user_documents_index(Request $request) {

        $base_query = \App\User::whereHas('userDocuments')->orderBy('updated_at','desc');
        
        if($request->search_key) {

            $base_query->where(function ($query) use ($request) {
                $query->where('name', "like", "%" . $request->search_key . "%");
                $query->orWhere('email', "like", "%" . $request->search_key . "%");
                $query->orWhere('mobile', "like", "%" . $request->search_key . "%");
            });
        }

        if($request->status!='') {

            $base_query->where('status', $request->status);
        }

        $users = $base_query->where('is_document_verified', '!=', USER_DOCUMENT_APPROVED)->paginate($this->take);

        foreach($users as $user){

            $user->documents_count = \App\UserDocument::where('user_id',$user->id)->count();
            
        }
        
        return view('admin.users.documents.index')
                    ->with('page','users-documents')
                    ->with('sub_page', '')
                    ->with('users', $users);    
    
    }

    /**
     * @method user_document_view()
     *
     * @uses Display the specified document
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - Stardom document Id
     *
     * @return view page
     */
    public function user_documents_view(Request $request) {

        try {

            $user = \App\User::find($request->user_id);

            if(!$user) {

                throw new Exception(tr('user_not_found'));

            }

            $user_documents = \App\UserDocument::where('user_id', $request->user_id)->orderBy('updated_at','desc')->get();

            return view('admin.users.documents.view')
                    ->with('page', 'users-documents')
                    ->with('sub_page', '')
                    ->with('user', $user)
                    ->with('user_documents', $user_documents);
            
        } catch (Exception $e) {


            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method user_documents_verify()
     *
     * @uses verify the stardom documents
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - Stardom Document Id
     *
     * @return redirect back page with status of the stardom verification
     */
    public function user_documents_verify(Request $request) {

        try {

            DB::beginTransaction();

            $user = \App\User::find($request->user_id);   
            
            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user->is_document_verified = $user->is_document_verified ? USER_DOCUMENT_APPROVED : USER_DOCUMENT_DECLINED;

            if($user->save()) {

                DB::commit();

                $status_message = $user->is_document_verified ? tr('approved'):tr('declined');

                $email_data['subject'] = tr('user_document_verification').' '.Setting::get('site_name');

                $email_data['email']  = $user->email ?? "-";

                $email_data['name']  = $user->name ?? "-";

                $email_data['page'] = "emails.users.document-verify";

                $email_data['message'] = tr('document_verify_message', $status_message); 

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $user->is_document_verified ? tr('user_document_verify_success') : tr('user_document_unverify_success');

                return redirect()->route('admin.user_documents.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_document_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.user_documents.index')->with('flash_error', $e->getMessage());

        }
    
    }



    /**
     * @method user_subscriptions_payments()
     *
     * @uses To list out users subscription payment details 
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function user_subscription_payments(Request $request) {
       
        $base_query = \App\UserSubscriptionPayment::orderBy('created_at','desc')
                      ->has('fromUser')->has('toUser');

        $search_key = $request->search_key;

        if($search_key) {

            $base_query = $base_query
                        ->whereHas('fromUser',function($query) use($search_key) {

                            return $query->where('users.name','LIKE','%'.$search_key.'%');

                        })->orwhereHas('toUser',function($query) use($search_key) {
                            
                            return $query->where('users.name','LIKE','%'.$search_key.'%');
                        });
        }

        $user_subscriptions = $base_query->paginate(10);


        return view('admin.users.subscriptions.index')
                    ->with('page', 'user_subscriptions')
                    ->with('sub_page', 'user-subscription-payments')
                    ->with('user_subscriptions', $user_subscriptions);
    }



    /**
     * @method user_subscriptions_payment_view()
     *
     * @uses To list out users subscription payment details 
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function user_subscriptions_payment_view(Request $request) {

        try {
       
            $user_subscription_payment = \App\UserSubscriptionPayment::find($request->subscription_id);
             
             if(!$user_subscription_payment) { 

                throw new Exception(tr('user_subscription_payment_not_found'), 101);                
            }


            return view('admin.users.subscriptions.view')
                        ->with('page', 'user_subscription_payment')
                        ->with('sub_page', 'user-subscription-payments')
                        ->with('user_subscription_payment', $user_subscription_payment);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
       }
    }

}
