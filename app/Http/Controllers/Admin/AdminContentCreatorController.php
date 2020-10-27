<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

class AdminContentCreatorController extends Controller
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
     * @method content_creators_index()
     *
     * @uses To list out content_creators details 
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
    public function content_creators_index(Request $request) {

        $base_query = \App\User::where('users.is_content_creator', YES)->orderBy('created_at','DESC');

        if($request->status) {

            switch ($request->status) {

                case SORT_BY_APPROVED:
                    $base_query = $base_query->where('status',APPROVED);
                    break;

                case SORT_BY_DECLINED:
                    $base_query = $base_query->where('status',DECLINED);
                    break;

                case SORT_BY_EMAIL_VERIFIED:
                    $base_query = $base_query->where('is_email_verified',CONTENT_CREATOR_EMAIL_VERIFIED);
                    break;
                
                default:
                    $base_query = $base_query->where('is_email_verified',CONTENT_CREATOR_EMAIL_NOT_VERIFIED);
                    break;
            }
        }

        $sub_page = 'content_creators-view';

        if($request->unverified) {

            $sub_page = 'content_creators-unverified';

            $base_query = $base_query->where('is_email_verified',CONTENT_CREATOR_EMAIL_NOT_VERIFIED);
        }

        if($request->search_key) {

            $base_query = $base_query
                    ->where('name','LIKE','%'.$request->search_key.'%')
                    ->orWhere('email','LIKE','%'.$request->search_key.'%')
                    ->orWhere('mobile','LIKE','%'.$request->search_key.'%');
        }
        
        $content_creators = $base_query->paginate(10);

        return view('admin.content_creators.index') 
                    ->with('page','content_creators')
                    ->with('sub_page', 'content_creators-view')
                    ->with('content_creators' , $content_creators);
    
    }

    /**
     * @method content_creators_create()
     *
     * @uses To create stardom details
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
    public function content_creators_create() {

        $content_creator_details = new \App\User;

        return view('admin.content_creators.create')
                    ->with('page' , 'content_creators')
                    ->with('sub_page','content_creators-create')
                    ->with('content_creator_details', $content_creator_details);           
    }

    /**
     * @method content_creators_edit()
     *
     * @uses To display and update content creator details based on the content creator id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Content creator Id
     * 
     * @return redirect view page 
     *
     */
    public function content_creators_edit(Request $request) {

        try {

            $content_creator_details = \App\User::find($request->user_id);

            if(!$content_creator_details) { 

                throw new Exception(tr('content_creator_not_found'), 101);
            }

            return view('admin.content_creators.edit')                    
                    ->with('page' , 'content_creators')
                    ->with('sub_page','content_creators-view')
                    ->with('content_creator_details' , $content_creator_details); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.content_creators.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method content_creators_save()
     *
     * @uses To save the content_creators details of new/existing content creator object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - content creator Form Data
     *
     * @return success message
     *
     */
    public function content_creators_save(Request $request) {
        
        try {

            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'email' => $request->user_id ? 'required|email|max:191|unique:users,email,'.$request->user_id.',id' : 'required|email|max:191|unique:users,email,NULL,id',
                'password' => $request->user_id ? "" : 'required|min:6|confirmed',
                'mobile' => $request->mobile ? 'digits_between:6,13' : '',
                'picture' => 'mimes:jpg,png,jpeg',
                'user_id' => 'exists:users,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $content_creator_details = $request->user_id ? \App\User::find($request->user_id) : new \App\User;

            if($content_creator_details->id) {

                $message = tr('content_creator_updated_success'); 

            } else {

                $content_creator_details->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('content_creator_created_success');

                $content_creator_details->email_verified_at = date('Y-m-d H:i:s');

                $content_creator_details->picture = asset('placeholder.jpeg');

                $content_creator_details->is_email_verified = CONTENT_CREATOR_EMAIL_VERIFIED;

                $content_creator_details->token = Helper::generate_token();

                $content_creator_details->token_expiry = Helper::generate_token_expiry();

                $content_creator_details->login_by = $request->login_by ?: 'manual';

                $content_creator_details->is_content_creator = YES;

            }

            $content_creator_details->name = $request->name ?: $content_creator_details->name;

            $content_creator_details->email = $request->email ?: $content_creator_details->email;

            $content_creator_details->mobile = $request->mobile ?: '';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_id) {

                    Helper::storage_delete_file($content_creator_details->picture, PROFILE_PATH_USER); 
                    // Delete the old pic
                }

                $content_creator_details->picture = Helper::storage_upload_file($request->file('picture'), PROFILE_PATH_USER);
            }

            if($content_creator_details->save()) {

                DB::commit(); 

                return redirect(route('admin.content_creators.view', ['user_id' => $content_creator_details->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('content_creator_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method content_creators_view()
     *
     * @uses displays the specified content_creator details based on content creator id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - content creator Id
     * 
     * @return View page
     *
     */
    public function content_creators_view(Request $request) {
       
        try {
      
            $content_creator_details = \App\User::find($request->user_id);

            if(!$content_creator_details) { 

                throw new Exception(tr('content_creator_not_found'), 101);                
            }

            return view('admin.content_creators.view')
                        ->with('page', 'content_creators') 
                        ->with('sub_page','content_creators-view') 
                        ->with('content_creator_details' , $content_creator_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method content_creators_delete()
     *
     * @uses delete the content creator details based on content creator id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Content Creator Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function content_creators_delete(Request $request) {

        try {

            DB::begintransaction();

            $user_details = \App\User::find($request->user_id);
            
            if(!$user_details) {

                throw new Exception(tr('content_creator_not_found'), 101);                
            }

            if($user_details->delete()) {

                DB::commit();

                return redirect()->route('admin.content_creators.index')->with('flash_success',tr('content_creator_deleted_success'));   

            } 
            
            throw new Exception(tr('content_creator_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method content_creators_status
     *
     * @uses To update content creator status as DECLINED/APPROVED based on content creator id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - content creator Id
     * 
     * @return response success/failure message
     *
     **/
    public function content_creators_status(Request $request) {

        try {
            
            DB::beginTransaction();

            $content_creator_details = \App\User::find($request->user_id);

            if(!$content_creator_details) {

                throw new Exception(tr('content_creator_details_not_found'), 101);
                
            }

            $content_creator_details->status = $content_creator_details->status ? DECLINED : APPROVED ;

            if($content_creator_details->save()) {

                DB::commit();

                $message = $content_creator_details->status ? tr('content_creator_approve_success') : tr('content_creator_decline_success');
                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('content_creator_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.content_creators.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method content_creators_verify_status()
     *
     * @uses verify the content creator
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - Content Creator Id
     *
     * @return redirect back page with status of the content details verification
     */
    public function content_creators_verify_status(Request $request) {

        try {

            DB::beginTransaction();

            $content_creator_details = \App\User::find($request->user_id);

            if(!$content_creator_details) {

                throw new Exception(tr('content_creator_details_not_found'), 101);
                
            }

            $content_creator_details->is_email_verified = $content_creator_details->is_email_verified ? CONTENT_CREATOR_EMAIL_NOT_VERIFIED : CONTENT_CREATOR_EMAIL_VERIFIED;

            if($content_creator_details->save()) {

                DB::commit();

                $message = $content_creator_details->is_email_verified ? tr('content_creator_verify_success') : tr('content_creator_unverify_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('content_creator_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.content_creators.index')->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method user_documents_index()
     *
     * @uses Lists all stradom documents 
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


        $base_query = \App\UserDocument::orderBy('created_at','DESC');

        $stardom_documents = $base_query->paginate(10);
       
        return view('admin.content_creators.documents.index')
                    
                    ->with('page','content_creators')
                    ->with('sub_page' , 'content_creators-documents')
                    ->with('stardom_documents' , $stardom_documents);
    
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
      
            $stardom_document_details = \App\UserDocument::find($request->stardom_document_id);

            if(!$stardom_document_details) { 

                throw new Exception(tr('stardom_document_not_found'), 101);                
            }

            return view('admin.users.documents.view')
                        
                        ->with('page', 'content_creators') 
                        ->with('sub_page','content_creators-documents') 
                        ->with('stardom_document_details' , $stardom_document_details);
            
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

            $stardom_document_details = \App\UserDocument::find($request->stardom_document_id);   
            
            if(!$stardom_document_details) {

                throw new Exception(tr('stardom_document_details_not_found'), 101);
                
            }

            $stardom_document_details->is_email_verified = $stardom_document_details->is_email_verified ? STARDOM_DOCUMENT_NOT_VERIFIED : STARDOM_DOCUMENT_VERIFIED;

            if($stardom_document_details->save()) {

                DB::commit();

                $email_data['subject'] = tr('stardom_document_verification' , Setting::get('site_name'));

                $email_data['email']  = $stardom_document_details->userDetails->email ?? "-";

                $email_data['name']  = $stardom_document_details->userDetails->name ?? "-";

                $email_data['page'] = "emails.users.document-verify";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $stardom_document_details->is_email_verified ? tr('stardom_document_verify_success') : tr('stardom_document_unverify_success');

                return redirect()->route('admin.users.documents.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('stardom_document_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.documents.index')->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method user_wallets_index()
     *
     * @uses Display the lists of stardom users
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function user_wallets_index(Request $request) {

        $base_query = \App\UserWallet::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query

                ->whereHas('userDetails', function($q) use ($search_key) {

                    return $q->Where('users.name','LIKE','%'.$search_key.'%');

                })->orWhere('user_wallets.unique_id','LIKE','%'.$search_key.'%');
                        
        }

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        $user_wallets = $base_query->paginate(10);

        return view('admin.user_wallets.index')
                    ->with('page','user_wallets')
                    ->with('user_wallets' , $user_wallets);
    }

    /**
     * @method user_wallets_view()
     *
     * @uses display the transaction details of the perticulor stardom
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - stardom_wallet_id
     * 
     * @return View page
     *
     */
    public function user_wallets_view(Request $request) {
       
        try {
            
            $user_wallet_details = \App\UserWallet::where('user_id',$request->user_id)->first();
           
            if(!$user_wallet_details) { 

                $user_wallet_details = new \App\UserWallet;

                $user_wallet_details->user_id = $request->user_id;

                $user_wallet_details->save();

            }

            $user_wallet_payments = \App\UserWalletPayment::where('user_id',$user_wallet_details->user_id)->paginate(10);
                   
            return view('admin.user_wallets.view')
                        ->with('page', 'user_wallets') 
                        ->with('user_wallet_details', $user_wallet_details)
                        ->with('user_wallet_payments', $user_wallet_payments);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method users_followers()
     *
     * @uses This is to display the all followers of specified content creator
     *
     * @created Ganesh
     *
     * @updated
     *
     * @param object $request - follower Id
     *
     * @return view page
     */
    public function users_followers(Request $request) {

        $followers = \App\Follower::where('follower_id', $request->follower_id)->paginate($this->take);

        $user = \App\User::find($request->user_id);
        
        return view('admin.content_creators.followers')
                ->with('page','content_creators')
                ->with('sub_page','content_creators-view')
                ->with('followers', $followers)
                ->with('user', $user);    
    }

    /**
     * @method users_followings()
     *
     * @uses This is to display the all followers of specified 
     *
     * @created Ganesh
     *
     * @updated
     *
     * @param object $request - follower Id
     *
     * @return view page
     */
    public function users_followings(Request $request) {

        $followings = \App\Follower::where('user_id',$request->user_id)->paginate($this->take);
        
        $user = \App\User::find($request->user_id);

        return view('admin.content_creators.followings')
                ->with('page', 'content_creators')
                ->with('sub_page', 'content_creators-view')
                ->with('followings', $followings)
                ->with('user', $user);
       
    }


    

}
