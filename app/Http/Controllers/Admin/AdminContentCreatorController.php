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
                    ->with('main_page','stardoms-crud')
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
                    ->with('main_page','stardoms-crud')
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
                    ->with('main_page','stardoms-crud')
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

                    Helper::storage_delete_file($stardom_details->picture, STARDOM_FILE_PATH); 
                    // Delete the old pic
                }

                $stardom_details->picture = Helper::storage_upload_file($request->file('picture'), STARDOM_FILE_PATH);
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
                        ->with('main_page','stardoms-crud')
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
     * @method content_creators_documents_index()
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
    public function content_creators_documents_index(Request $request) {


        $base_query = \App\UserDocument::orderBy('created_at','DESC');

        $stardom_documents = $base_query->paginate(10);
       
        return view('admin.users.documents.index')
                    ->with('main_page','stardoms-crud')
                    ->with('page','stardoms')
                    ->with('sub_page' , 'stardoms-documents')
                    ->with('stardom_documents' , $stardom_documents);
    
    }

    /**
     * @method content_creators_document_view()
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
    public function content_creators_document_view(Request $request) {

        try {
      
            $stardom_document_details = \App\UserDocument::find($request->stardom_document_id);

            if(!$stardom_document_details) { 

                throw new Exception(tr('stardom_document_not_found'), 101);                
            }

            return view('admin.users.documents.view')
                        ->with('main_page','stardoms-crud')
                        ->with('page', 'stardoms') 
                        ->with('sub_page','stardoms-documents') 
                        ->with('stardom_document_details' , $stardom_document_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

     /**
     * @method content_creators_documents_verify()
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
    public function content_creators_documents_verify(Request $request) {

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
     * @method user_products_index()
     *
     * @uses To list out stardom products details 
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
    public function user_products_index(Request $request) {

        $base_query = \App\UserProduct::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('userDetails',function($query) use($search_key) {

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('user_products.name','LIKE','%'.$search_key.'%');
        }

        if($request->user_id){

            $base_query = $base_query->where('user_id',$request->user_id);
        }
       
        $user_products = $base_query->paginate(10);

        return view('admin.user_products.index')
                ->with('main_page','user_products-crud')
                ->with('page','user_products')
                ->with('sub_page' , 'user_products-view')
                ->with('user_products' , $user_products);
    }

    /**
     * @method user_products_create()
     *
     * @uses To create stardom product details
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
    public function user_products_create() {

        $user_product_details = new \App\UserProduct;

        $users = \App\User::where('is_content_creator', YES)->where('status', APPROVED)->get();

        return view('admin.user_products.create')
                ->with('main_page','user_products-crud')
                ->with('page' , 'user_products')
                ->with('sub_page', 'user_products-create')
                ->with('user_product_details', $user_product_details)
                ->with('users',$users);           
    }

    /**
     * @method user_products_edit()
     *
     * @uses To display and update stardom product details based on the stardom product id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return redirect view page 
     *
     */
    public function user_products_edit(Request $request) {

        try {

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(tr('user_product_not_found'), 101);
            }

            $users = \App\User::where('is_content_creator', YES)->where('status', APPROVED)->get();

            foreach ($users as $key => $user_details) {

                $user_details->is_selected = NO;

                if($user_product_details->user_id == $user_details->id){
                    
                    $user_details->is_selected = YES;
                }

            }
            return view('admin.user_products.edit')
                ->with('main_page','user_products-crud')
                ->with('page' , 'user_products')
                ->with('sub_page', 'user_products-view')
                ->with('user_product_details', $user_product_details)
                ->with('users', $users); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.user_products.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method user_products_save()
     *
     * @uses To save the stardom products details of new/existing stardom product object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - Stardom Product Form Data
     *
     * @return success message
     *
     */
    public function user_products_save(Request $request) {
        
        try {
            
            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'quantity' => 'required|max:100',
                'price' => 'required|max:100',
                'picture' => 'mimes:jpg,png,jpeg',
                'discription' => 'max:199',
                'user_id' => 'required',
                'user_id' => 'exists:users,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = $request->user_product_id ? \App\UserProduct::find($request->user_product_id) : new \App\UserProduct;

            if($user_product_details->id) {

                $message = tr('user_product_updated_success'); 

            } else {

                $message = tr('user_product_created_success');

            }

            $user_product_details->user_id = $request->user_id ?: $user_product_details->user_id;

            $user_product_details->name = $request->name ?: $user_product_details->name;

            $user_product_details->quantity = $request->quantity ?: $user_product_details->quantity;

            $user_product_details->price = $request->price ?: '';

            $user_product_details->description = $request->description ?: '';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_product_id) {

                    Helper::storage_delete_file($user_product_details->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $user_product_details->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($user_product_details->save()) {

                DB::commit(); 

                return redirect(route('admin.user_products.view', ['user_product_id' => $user_product_details->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('user_product_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method user_products_view()
     *
     * @uses displays the specified user product details based on user product id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - user product Id
     * 
     * @return View page
     *
     */
    public function user_products_view(Request $request) {
       
        try {
      
            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(tr('user_product_not_found'), 101);                
            }

            return view('admin.user_products.view')
                    ->with('main_page','user_products-crud')
                    ->with('page', 'user_products') 
                    ->with('sub_page','user_products-view')
                    ->with('user_product_details' , $user_product_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method user_products_delete()
     *
     * @uses delete the stardom product details based on stardom id
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
    public function user_products_delete(Request $request) {

        try {

            DB::begintransaction();

            $user_product_details = \App\UserProduct::find($request->user_product_id);
            
            if(!$user_product_details) {

                throw new Exception(tr('user_product_not_found'), 101);                
            }

            if($user_product_details->delete()) {

                DB::commit();

                return redirect()->route('admin.user_products.index')->with('flash_success',tr('user_product_deleted_success'));   

            } 
            
            throw new Exception(tr('user_product_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method user_products_status
     *
     * @uses To update stardom product status as DECLINED/APPROVED based on stardom product id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function user_products_status(Request $request) {

        try {

            DB::beginTransaction();

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) {

                throw new Exception(tr('user_product_not_found'), 101);
                
            }

            $user_product_details->status = $user_product_details->status ? DECLINED : APPROVED ;

            if($user_product_details->save()) {

                DB::commit();

                if($user_product_details->status == DECLINED) {

                    $email_data['subject'] = tr('product_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('product_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');
                }

                $email_data['email']  = $user_product_details->userDetails->email ?? "-";

                $email_data['name']  = $user_product_details->userDetails->name ?? "-";

                $email_data['product_name']  = $user_product_details->name;

                $email_data['page'] = "emails.products.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $user_product_details->status ? tr('user_product_approve_success') : tr('user_product_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_product_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.user_products.index')->with('flash_error', $e->getMessage());

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
            
            $stardom_wallet_details = \App\UserWallet::where('user_id',$request->user_id)->first();
           
            if(!$stardom_wallet_details) { 

                throw new Exception(tr('stardom_wallet_details_not_found'), 101);                
            }

            $stardom_wallet_payments = \App\UserWalletPayment::where('user_id',$stardom_wallet_details->user_id)->paginate(10);
                   
            return view('admin.user_wallets.view')
                        ->with('page', 'user_wallets') 
                        ->with('stardom_wallet_details', $stardom_wallet_details)
                        ->with('stardom_wallet_payments', $stardom_wallet_payments);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }


    /**
     * @method user_products_dashboard()
     *
     * @uses 
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
    public function user_products_dashboard(Request $request) {

        try {

            $user_product_details = \App\UserProduct::where('id',$request->user_product_id)->first();

            if(!$user_product_details) {

                throw new Exception(tr('user_product_details_not_found'), 101);
            }

            $data = new \stdClass;

            $data->total_orders = \App\OrderProduct::where('user_product_id',$user_product_details->id)->count();

            $data->today_orders = \App\OrderProduct::where('user_product_id',$user_product_details->id)->whereDate('created_at',today())->count();

            $order_products_ids =  \App\OrderProduct::where('user_product_id',$user_product_details->id)->pluck('order_id');

            $data->total_revenue = $order_products_ids->count() > 0 ? \App\OrderPayment::whereIn('order_id',[$order_products_ids])->sum('total') : 0;

            $data->today_revenue = count($order_products_ids) > 0 ? \App\OrderPayment::whereIn('order_id',[$order_products_ids])->where('created_at',today())->sum('total') : 0;

            $ids = count($order_products_ids)> 0 ? $order_products_ids : 0 ;
            
            $data->analytics = last_x_days_revenue(6,$ids);
           
            return view('admin.user_products.dashboard')
                        ->with('main_page','user_products-crud')
                        ->with('page','user_products')
                        ->with('sub_page' , 'user_products-view')
                        ->with('data', $data);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method order_products
     *
     * @uses Display all orders based the product details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function order_products(Request $request) {

        try {

            DB::beginTransaction();

            $order_products = \App\OrderProduct::where('user_product_id',$request->user_product_id)->get();

            if(!$order_products) {

                throw new Exception(tr('user_product_not_found'), 101);
                
            }

            return view('admin.user_products.order_products')
                        ->with('main_page','user_products-crud')
                        ->with('page','user_products')
                        ->with('sub_page' , 'user_products-view')
                        ->with('order_products', $order_products);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.user_products.index')->with('flash_error', $e->getMessage());

        }

    }


}
