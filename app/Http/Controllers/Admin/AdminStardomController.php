<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

class AdminStardomController extends Controller
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
     * @method stardoms_index()
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
    public function stardoms_index(Request $request) {

        $base_query = \App\Stardom::orderBy('created_at','DESC');

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

        return view('admin.stardoms.index')
                    ->with('main_page','stardoms-crud')
                    ->with('page','stardoms')
                    ->with('sub_page' , $sub_page)
                    ->with('stardoms' , $stardoms);
    }

    /**
     * @method stardoms_create()
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
    public function stardoms_create() {

        $stardom_details = new \App\Stardom;

        return view('admin.stardoms.create')
                    ->with('main_page','stardoms-crud')
                    ->with('page' , 'stardoms')
                    ->with('sub_page','stardoms-create')
                    ->with('stardom_details', $stardom_details);           
    }

    /**
     * @method stardoms_edit()
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
    public function stardoms_edit(Request $request) {

        try {

            $stardom_details = \App\Stardom::find($request->stardom_id);

            if(!$stardom_details) { 

                throw new Exception(tr('stardom_not_found'), 101);
            }

            return view('admin.stardoms.edit')
                    ->with('main_page','stardoms-crud')
                    ->with('page' , 'stardoms')
                    ->with('sub_page','stardoms-view')
                    ->with('stardom_details' , $stardom_details); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.stardoms.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method stardoms_save()
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
    public function stardoms_save(Request $request) {
        
        try {

            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'email' => $request->stardom_id ? 'required|email|max:191|unique:stardoms,email,'.$request->stardom_id.',id' : 'required|email|max:191|unique:stardoms,email,NULL,id',
                'password' => $request->stardom_id ? "" : 'required|min:6|confirmed',
                'mobile' => $request->mobile ? 'digits_between:6,13' : '',
                'picture' => 'mimes:jpg,png,jpeg',
                'stardom_id' => 'exists:stardoms,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $stardom_details = $request->stardom_id ? \App\Stardom::find($request->stardom_id) : new \App\Stardom;

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

                if($request->stardom_id) {

                    Helper::storage_delete_file($stardom_details->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $stardom_details->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($stardom_details->save()) {

                if($is_new_stardom == YES) {

                    /**
                     * @todo Welcome mail notification
                     */

                    $email_data['subject'] = tr('stardom_welcome_email' , Setting::get('site_name'));

                    $email_data['email']  = $stardom_details->email;

                    $email_data['name'] = $stardom_details->name;

                    $email_data['page'] = "emails.stardoms.welcome";

                    $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    $stardom_details->is_verified = STARDOM_EMAIL_VERIFIED;

                    $stardom_details->save();

                }

                DB::commit(); 

                return redirect(route('admin.stardoms.view', ['stardom_id' => $stardom_details->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('stardom_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method stardoms_view()
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
    public function stardoms_view(Request $request) {
       
        try {
      
            $stardom_details = \App\Stardom::find($request->stardom_id);

            if(!$stardom_details) { 

                throw new Exception(tr('stardom_not_found'), 101);                
            }

            return view('admin.stardoms.view')
                        ->with('main_page','stardoms-crud')
                        ->with('page', 'stardoms') 
                        ->with('sub_page','stardoms-view') 
                        ->with('stardom_details' , $stardom_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method stardoms_delete()
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
    public function stardoms_delete(Request $request) {

        try {

            DB::begintransaction();

            $stardom_details = \App\Stardom::find($request->stardom_id);
            
            if(!$stardom_details) {

                throw new Exception(tr('stardom_not_found'), 101);                
            }

            if($stardom_details->delete()) {

                $email_data['subject'] = tr('stardom_delete_email' , Setting::get('site_name'));

                $email_data['email']  = $stardom_details->email;

                $email_data['page'] = "emails.stardoms.stardom-delete";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                return redirect()->route('admin.stardoms.index')->with('flash_success',tr('stardom_deleted_success'));   

            } 
            
            throw new Exception(tr('stardom_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method stardoms_status
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
    public function stardoms_status(Request $request) {

        try {
            
            DB::beginTransaction();

            $stardom_details = \App\Stardom::find($request->stardom_id);

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

                $email_data['page'] = "emails.stardoms.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                $message = $stardom_details->status ? tr('stardom_approve_success') : tr('stardom_decline_success');
                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('stardom_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.stardoms.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method stardoms_verify_status()
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
    public function stardoms_verify_status(Request $request) {

        try {

            DB::beginTransaction();

            $stardom_details = \App\Stardom::find($request->stardom_id);

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

            return redirect()->route('admin.stardoms.index')->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method stardoms_documents_index()
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
    public function stardoms_documents_index(Request $request) {


        $base_query = \App\StardomDocument::orderBy('created_at','DESC');

        $stardom_documents = $base_query->paginate(10);
       
        return view('admin.stardoms.documents.index')
                    ->with('main_page','stardoms-crud')
                    ->with('page','stardoms')
                    ->with('sub_page' , 'stardoms-documents')
                    ->with('stardom_documents' , $stardom_documents);
    
    }

    /**
     * @method stardoms_document_view()
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
    public function stardoms_document_view(Request $request) {

        try {
      
            $stardom_document_details = \App\StardomDocument::find($request->stardom_document_id);

            if(!$stardom_document_details) { 

                throw new Exception(tr('stardom_document_not_found'), 101);                
            }

            return view('admin.stardoms.documents.view')
                        ->with('main_page','stardoms-crud')
                        ->with('page', 'stardoms') 
                        ->with('sub_page','stardoms-documents') 
                        ->with('stardom_document_details' , $stardom_document_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

     /**
     * @method stardoms_documents_verify()
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
    public function stardoms_documents_verify(Request $request) {

        try {

            DB::beginTransaction();

            $stardom_document_details = \App\StardomDocument::find($request->stardom_document_id);   
            
            if(!$stardom_document_details) {

                throw new Exception(tr('stardom_document_details_not_found'), 101);
                
            }

            $stardom_document_details->is_verified = $stardom_document_details->is_verified ? STARDOM_DOCUMENT_NOT_VERIFIED : STARDOM_DOCUMENT_VERIFIED;

            if($stardom_document_details->save()) {

                DB::commit();

                $email_data['subject'] = tr('stardom_document_verification' , Setting::get('site_name'));

                $email_data['email']  = $stardom_document_details->stardomDetails->email ?? "-";

                $email_data['name']  = $stardom_document_details->stardomDetails->name ?? "-";

                $email_data['page'] = "emails.stardoms.document-verify";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $stardom_document_details->is_verified ? tr('stardom_document_verify_success') : tr('stardom_document_unverify_success');

                return redirect()->route('admin.stardoms.documents.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('stardom_document_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.stardoms.documents.index')->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method stardom_products_index()
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
    public function stardom_products_index(Request $request) {

        $base_query = \App\StardomProduct::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('stardomDetails',function($query) use($search_key) {

                return $query->where('stardoms.name','LIKE','%'.$search_key.'%');

            })->orWhere('stardom_products.name','LIKE','%'.$search_key.'%');
        }

        if($request->stardom_id){

            $base_query = $base_query->where('stardom_id',$request->stardom_id);
        }
       
        $stardom_products = $base_query->paginate(10);

        return view('admin.stardom_products.index')
                ->with('main_page','stardom_products-crud')
                ->with('page','stardom_products')
                ->with('sub_page' , 'stardom_products-view')
                ->with('stardom_products' , $stardom_products);
    }

    /**
     * @method stardom_products_create()
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
    public function stardom_products_create() {

        $stardom_product_details = new \App\StardomProduct;

        $stardoms = \App\Stardom::where('status', APPROVED)->get();

        return view('admin.stardom_products.create')
                ->with('main_page','stardom_products-crud')
                ->with('page' , 'stardom_products')
                ->with('sub_page','stardom_products-create')
                ->with('stardom_product_details', $stardom_product_details)
                ->with('stardoms',$stardoms);           
    }

    /**
     * @method stardom_products_edit()
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
    public function stardom_products_edit(Request $request) {

        try {

            $stardom_product_details = \App\StardomProduct::find($request->stardom_product_id);

            if(!$stardom_product_details) { 

                throw new Exception(tr('stardom_product_not_found'), 101);
            }

            $stardoms = \App\Stardom::where('status', APPROVED)->get();

            foreach ($stardoms as $key => $stardom_details) {

                $stardom_details->is_selected = NO;

                if($stardom_product_details->stardom_id == $stardom_details->id){
                    
                    $stardom_details->is_selected = YES;
                }

            }
            return view('admin.stardom_products.edit')
                ->with('main_page','stardom_products-crud')
                ->with('page' , 'stardom_products')
                ->with('sub_page','stardom_products-view')
                ->with('stardom_product_details' , $stardom_product_details)
                ->with('stardoms',$stardoms); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.stardom_products.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method stardom_products_save()
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
    public function stardom_products_save(Request $request) {
        
        try {
            
            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'quantity' => 'required|max:100',
                'price' => 'required|max:100',
                'picture' => 'mimes:jpg,png,jpeg',
                'discription' => 'max:199',
                'stardom_id' => 'required',
                'stardom_id' => 'exists:stardoms,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $stardom_product_details = $request->stardom_product_id ? \App\StardomProduct::find($request->stardom_product_id) : new \App\StardomProduct;

            if($stardom_product_details->id) {

                $message = tr('stardom_product_updated_success'); 

            } else {

                $message = tr('stardom_product_created_success');

            }

            $stardom_product_details->stardom_id = $request->stardom_id ?: $stardom_product_details->stardom_id;

            $stardom_product_details->name = $request->name ?: $stardom_product_details->name;

            $stardom_product_details->quantity = $request->quantity ?: $stardom_product_details->quantity;

            $stardom_product_details->price = $request->price ?: '';

            $stardom_product_details->description = $request->description ?: '';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->stardom_product_id) {

                    Helper::storage_delete_file($stardom_product_details->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $stardom_product_details->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($stardom_product_details->save()) {

                DB::commit(); 

                return redirect(route('admin.stardom_products.view', ['stardom_product_id' => $stardom_product_details->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('stardom_product_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method stardom_products_view()
     *
     * @uses displays the specified stardom product details based on stardom product id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - stardom product Id
     * 
     * @return View page
     *
     */
    public function stardom_products_view(Request $request) {
       
        try {
      
            $stardom_product_details = \App\StardomProduct::find($request->stardom_product_id);

            if(!$stardom_product_details) { 

                throw new Exception(tr('stardom_product_not_found'), 101);                
            }

            return view('admin.stardom_products.view')
                    ->with('main_page','stardom_products-crud')
                    ->with('page', 'stardom_products') 
                    ->with('sub_page','stardom_products-view')
                    ->with('stardom_product_details' , $stardom_product_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method stardom_products_delete()
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
    public function stardom_products_delete(Request $request) {

        try {

            DB::begintransaction();

            $stardom_product_details = \App\StardomProduct::find($request->stardom_product_id);
            
            if(!$stardom_product_details) {

                throw new Exception(tr('stardom_product_not_found'), 101);                
            }

            if($stardom_product_details->delete()) {

                DB::commit();

                return redirect()->route('admin.stardom_products.index')->with('flash_success',tr('stardom_product_deleted_success'));   

            } 
            
            throw new Exception(tr('stardom_product_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method stardom_products_status
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
    public function stardom_products_status(Request $request) {

        try {

            DB::beginTransaction();

            $stardom_product_details = \App\StardomProduct::find($request->stardom_product_id);

            if(!$stardom_product_details) {

                throw new Exception(tr('stardom_product_not_found'), 101);
                
            }

            $stardom_product_details->status = $stardom_product_details->status ? DECLINED : APPROVED ;

            if($stardom_product_details->save()) {

                DB::commit();

                if($stardom_product_details->status == DECLINED) {

                    $email_data['subject'] = tr('product_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('product_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');
                }

                $email_data['email']  = $stardom_product_details->stardomDetails->email ?? "-";

                $email_data['name']  = $stardom_product_details->stardomDetails->name ?? "-";

                $email_data['product_name']  = $stardom_product_details->name;

                $email_data['page'] = "emails.products.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $stardom_product_details->status ? tr('stardom_product_approve_success') : tr('stardom_product_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('stardom_product_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.stardom_products.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method stardom_wallets_index()
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
    public function stardom_wallets_index(Request $request) {

        $base_query = \App\StardomWallet::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query

                ->whereHas('stardomDetails', function($q) use ($search_key) {

                    return $q->Where('stardoms.name','LIKE','%'.$search_key.'%');

                })->orWhere('stardom_wallets.unique_id','LIKE','%'.$search_key.'%');
                        
        }

        if($request->stardom_id) {

            $base_query = $base_query->where('stardom_id',$request->stardom_id);
        }

        $stardom_wallets = $base_query->paginate(10);

        return view('admin.stardom_wallets.index')
                    ->with('page','stardom_wallets')
                    ->with('stardom_wallets' , $stardom_wallets);
    }

    /**
     * @method stardom_wallets_view()
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
    public function stardom_wallets_view(Request $request) {
       
        try {
            
            $stardom_wallet_details = \App\StardomWallet::where('stardom_id',$request->stardom_id)->first();
           
            if(!$stardom_wallet_details) { 

                throw new Exception(tr('stardom_wallet_details_not_found'), 101);                
            }

            $stardom_wallet_payments = \App\StardomWalletPayment::where('stardom_id',$stardom_wallet_details->stardom_id)->paginate(10);
                   
            return view('admin.stardom_wallets.view')
                        ->with('page', 'stardom_wallets') 
                        ->with('stardom_wallet_details' , $stardom_wallet_details)
                        ->with('stardom_wallet_payments',$stardom_wallet_payments);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }


}
