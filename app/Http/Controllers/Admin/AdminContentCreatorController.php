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
     * @uses To list out stardoms details 
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

        $base_query = \App\ContentCreator::orderBy('created_at','DESC');

        if($request->status) {

            switch ($request->status) {

                case SORT_BY_APPROVED:
                    $base_query = $base_query->where('status',APPROVED);
                    break;

                case SORT_BY_DECLINED:
                    $base_query = $base_query->where('status',DECLINED);
                    break;

                case SORT_BY_EMAIL_VERIFIED:
                    $base_query = $base_query->where('is_verified',STARDOM_EMAIL_VERIFIED);
                    break;
                
                default:
                    $base_query = $base_query->where('is_verified',STARDOM_EMAIL_NOT_VERIFIED);
                    break;
            }
        }

        $sub_page = 'stardoms-view';

        if($request->unverified) {

            $sub_page = 'stardoms-unverified';

            $base_query = $base_query->where('is_verified',STARDOM_EMAIL_NOT_VERIFIED);
        }

        if($request->search_key) {

            $base_query = $base_query
                    ->where('name','LIKE','%'.$request->search_key.'%')
                    ->orWhere('email','LIKE','%'.$request->search_key.'%')
                    ->orWhere('mobile','LIKE','%'.$request->search_key.'%');
        }
        
        $stardoms = $base_query->paginate(10);

        return view('admin.users.index')
                    
                    ->with('page','stardoms')
                    ->with('sub_page' , $sub_page)
                    ->with('stardoms' , $stardoms);
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

        $stardom_details = new \App\Stardom;

        return view('admin.users.create')
                    ->with('page' , 'stardoms')
                    ->with('sub_page','stardoms-create')
                    ->with('stardom_details', $stardom_details);           
    }

    /**
     * @method content_creators_edit()
     *
     * @uses To display and update stardom details based on the stardom id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Id
     * 
     * @return redirect view page 
     *
     */
    public function content_creators_edit(Request $request) {

        try {

            $stardom_details = \App\ContentCreator::find($request->user_id);

            if(!$stardom_details) { 

                throw new Exception(tr('stardom_not_found'), 101);
            }

            return view('admin.users.edit')                    
                    ->with('page' , 'stardoms')
                    ->with('sub_page','stardoms-view')
                    ->with('stardom_details' , $stardom_details); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method content_creators_save()
     *
     * @uses To save the stardoms details of new/existing stardom object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - Stardom Form Data
     *
     * @return success message
     *
     */
    public function content_creators_save(Request $request) {
        
        try {

            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'email' => $request->user_id ? 'required|email|max:191|unique:stardoms,email,'.$request->user_id.',id' : 'required|email|max:191|unique:stardoms,email,NULL,id',
                'password' => $request->user_id ? "" : 'required|min:6|confirmed',
                'mobile' => $request->mobile ? 'digits_between:6,13' : '',
                'picture' => 'mimes:jpg,png,jpeg',
                'user_id' => 'exists:users,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $stardom_details = $request->user_id ? \App\ContentCreator::find($request->user_id) : new \App\Stardom;

            $is_new_stardom = NO;

            if($stardom_details->id) {

                $message = tr('stardom_updated_success'); 

            } else {

                $is_new_stardom = YES;

                $stardom_details->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('stardom_created_success');

                $stardom_details->email_verified_at = date('Y-m-d H:i:s');

                $stardom_details->picture = asset('placeholder.jpeg');

                $stardom_details->is_verified = STARDOM_EMAIL_VERIFIED;

                $stardom_details->token = Helper::generate_token();

                $stardom_details->token_expiry = Helper::generate_token_expiry();

            }

            $stardom_details->name = $request->name ?: $stardom_details->name;

            $stardom_details->email = $request->email ?: $stardom_details->email;

            $stardom_details->mobile = $request->mobile ?: '';

            $stardom_details->login_by = $request->login_by ?: 'manual';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_id) {

                    Helper::storage_delete_file($stardom_details->picture, PROFILE_PATH_USER); 
                    // Delete the old pic
                }

                $stardom_details->picture = Helper::storage_upload_file($request->file('picture'), PROFILE_PATH_USER);
            }

            if($stardom_details->save()) {

                if($is_new_stardom == YES) {

                    /**
                     * @todo Welcome mail notification
                     */

                    $email_data['subject'] = tr('stardom_welcome_email' , Setting::get('site_name'));

                    $email_data['email']  = $stardom_details->email;

                    $email_data['name'] = $stardom_details->name;

                    $email_data['page'] = "emails.users.welcome";

                    $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    $stardom_details->is_verified = STARDOM_EMAIL_VERIFIED;

                    $stardom_details->save();

                }

                DB::commit(); 

                return redirect(route('admin.users.view', ['user_id' => $stardom_details->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('stardom_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method content_creators_view()
     *
     * @uses displays the specified stardom details based on stardom id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - stardom Id
     * 
     * @return View page
     *
     */
    public function content_creators_view(Request $request) {
       
        try {
      
            $stardom_details = \App\ContentCreator::find($request->user_id);

            if(!$stardom_details) { 

                throw new Exception(tr('stardom_not_found'), 101);                
            }

            return view('admin.users.view')
                        
                        ->with('page', 'stardoms') 
                        ->with('sub_page','stardoms-view') 
                        ->with('stardom_details' , $stardom_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method content_creators_delete()
     *
     * @uses delete the stardom details based on stardom id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Stardom Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function content_creators_delete(Request $request) {

        try {

            DB::begintransaction();

            $stardom_details = \App\ContentCreator::find($request->user_id);
            
            if(!$stardom_details) {

                throw new Exception(tr('stardom_not_found'), 101);                
            }

            if($stardom_details->delete()) {

                $email_data['subject'] = tr('stardom_delete_email' , Setting::get('site_name'));

                $email_data['email']  = $stardom_details->email;

                $email_data['page'] = "emails.users.stardom-delete";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                return redirect()->route('admin.users.index')->with('flash_success',tr('stardom_deleted_success'));   

            } 
            
            throw new Exception(tr('stardom_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method content_creators_status
     *
     * @uses To update stardom status as DECLINED/APPROVED based on stardom id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Id
     * 
     * @return response success/failure message
     *
     **/
    public function content_creators_status(Request $request) {

        try {
            
            DB::beginTransaction();

            $stardom_details = \App\ContentCreator::find($request->user_id);

            if(!$stardom_details) {

                throw new Exception(tr('stardom_not_found'), 101);
                
            }

            $stardom_details->status = $stardom_details->status ? DECLINED : APPROVED ;

            if($stardom_details->save()) {

                if($stardom_details->status == DECLINED) {

                    $email_data['subject'] = tr('stardom_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('stardom_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');
                }

                $email_data['email']  = $stardom_details->email;

                $email_data['name']  = $stardom_details->name;

                $email_data['page'] = "emails.users.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                $message = $stardom_details->status ? tr('stardom_approve_success') : tr('stardom_decline_success');
                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('stardom_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method content_creators_verify_status()
     *
     * @uses verify the stardom
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - Stardom Id
     *
     * @return redirect back page with status of the stardom verification
     */
    public function content_creators_verify_status(Request $request) {

        try {

            DB::beginTransaction();

            $stardom_details = \App\ContentCreator::find($request->user_id);

            if(!$stardom_details) {

                throw new Exception(tr('stardom_details_not_found'), 101);
                
            }

            $stardom_details->is_verified = $stardom_details->is_verified ? STARDOM_EMAIL_NOT_VERIFIED : STARDOM_EMAIL_VERIFIED;

            if($stardom_details->save()) {

                DB::commit();

                $message = $stardom_details->is_verified ? tr('stardom_verify_success') : tr('stardom_unverify_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('stardom_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

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
       
        return view('admin.users.documents.index')
                    
                    ->with('page','stardoms')
                    ->with('sub_page' , 'stardoms-documents')
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
                        
                        ->with('page', 'stardoms') 
                        ->with('sub_page','stardoms-documents') 
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

            $stardom_document_details->is_verified = $stardom_document_details->is_verified ? STARDOM_DOCUMENT_NOT_VERIFIED : STARDOM_DOCUMENT_VERIFIED;

            if($stardom_document_details->save()) {

                DB::commit();

                $email_data['subject'] = tr('stardom_document_verification' , Setting::get('site_name'));

                $email_data['email']  = $stardom_document_details->userDetails->email ?? "-";

                $email_data['name']  = $stardom_document_details->userDetails->name ?? "-";

                $email_data['page'] = "emails.users.document-verify";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $stardom_document_details->is_verified ? tr('stardom_document_verify_success') : tr('stardom_document_unverify_success');

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

                throw new Exception(tr('user_wallet_details_not_found'), 101);                
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

}
