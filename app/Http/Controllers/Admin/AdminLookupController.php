<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

class AdminLookupController extends Controller
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
     * @method documents_index()
     *
     * @uses To list out decoments details 
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
    public function documents_index(Request $request) {

        $documents = \App\Document::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.documents.index')
                    ->with('page','documents')
                    ->with('sub_page', 'documents-view')
                    ->with('documents', $documents);
    }

    /**
     * @method documents_create()
     *
     * @uses To create documents details
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
    public function documents_create() {

        $document_details = new \App\Document;

        return view('admin.documents.create')
                    ->with('page', 'documents')
                    ->with('sub_page','documents-create')
                    ->with('document_details', $document_details);           
    }

    /**
     * @method documents_edit()
     *
     * @uses To display and update documents details based on the document id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Document Id
     * 
     * @return redirect view page 
     *
     */
    public function documents_edit(Request $request) {

        try {

            $document_details = \App\Document::find($request->document_id);

            if(!$document_details) { 

                throw new Exception(tr('document_not_found'), 101);
            }

            return view('admin.documents.edit')
                    ->with('page', 'documents')
                    ->with('sub_page','documents-create')
                    ->with('document_details' , $document_details); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.documents.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method documents_save()
     *
     * @uses To save the document details of new/existing document object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - Document Form Data
     *
     * @return success message
     *
     */
    public function documents_save(Request $request) {
            
        try {

            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'picture' => 'mimes:jpg,png,jpeg',
                'document_id' => 'exists:documents,id|nullable',
                'description' => 'max:199',
            ];

            Helper::custom_validator($request->all(),$rules);

            $document_details = $request->document_id ? \App\Document::find($request->document_id) : new \App\Document;

            if($document_details->id) {

                $message = tr('document_updated_success'); 

            } else {

                $message = tr('document_created_success');

                $document_details->picture = asset('document.jpeg');

            }

            $document_details->name = $request->name ?: $document_details->name;

            $document_details->description = $request->description ?: $document_details->description;

            $document_details->is_required = $request->is_required == YES ? YES : NO;

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_id) {

                    Helper::storage_delete_file($document_details->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $document_details->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($document_details->save()) {

                DB::commit(); 

                return redirect(route('admin.documents.view', ['document_id' => $document_details->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('document_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method documents_view()
     *
     * @uses displays the specified document details based on dosument id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - document Id
     * 
     * @return View page
     *
     */
    public function documents_view(Request $request) {
       
        try {
      
            $document_details = \App\Document::find($request->document_id);

            if(!$document_details) { 

                throw new Exception(tr('document_not_found'), 101);                
            }

            return view('admin.documents.view')
                        ->with('page', 'documents') 
                        ->with('sub_page', 'documents-view') 
                        ->with('document_details', $document_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method documents_delete()
     *
     * @uses delete the document details based on document id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - document Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function documents_delete(Request $request) {

        try {

            DB::begintransaction();

            $document_details = \App\Document::find($request->document_id);
            
            if(!$document_details) {

                throw new Exception(tr('document_not_found'), 101);                
            }

            if($document_details->delete()) {

                DB::commit();

                return redirect()->route('admin.documents.index')->with('flash_success',tr('document_deleted_success'));   

            } 
            
            throw new Exception(tr('document_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method documents_status
     *
     * @uses To update document status as DECLINED/APPROVED based on document id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Document Id
     * 
     * @return response success/failure message
     *
     **/
    public function documents_status(Request $request) {

        try {

            DB::beginTransaction();

            $document_details = \App\Document::find($request->document_id);

            if(!$document_details) {

                throw new Exception(tr('document_not_found'), 101);
                
            }

            $document_details->status = $document_details->status ? DECLINED : APPROVED ;

            if($document_details->save()) {

                DB::commit();

                $message = $document_details->status ? tr('document_approve_success') : tr('document_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('document_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.documents.index')->with('flash_error', $e->getMessage());

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

        $static_pages = \App\StaticPage::orderBy('updated_at' , 'desc')->paginate(10);

        return view('admin.static_pages.index')
                    ->with('page', 'static_pages')
                    ->with('sub_page', 'static_pages-view')
                    ->with('static_pages', $static_pages);
    
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

            $check_page = \App\StaticPage::where('type', $static_key)->first();

            if($check_page) {
                unset($static_keys[$key]);
            }
        }

        $static_keys[] = 'others';

        $static_page_details = new \App\StaticPage;

        return view('admin.static_pages.create')
                ->with('page', 'static_pages')
                ->with('sub_page', 'static_pages-create')
                ->with('static_keys', $static_keys)
                ->with('static_page_details', $static_page_details);
   
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

            $static_page_details = \App\StaticPage::find($request->static_page_id);

            if(!$static_page_details) {

                throw new Exception(tr('static_page_not_found'), 101);
            }

            $static_keys = ['about' , 'contact' , 'privacy' , 'terms' , 'help' , 'faq' , 'refund', 'cancellation'];

            foreach ($static_keys as $key => $static_key) {

                // Check the record exists

                $check_page = \App\StaticPage::where('type', $static_key)->first();

                if($check_page) {
                    unset($static_keys[$key]);
                }
            }

            $static_keys[] = 'others';

            $static_keys[] = $static_page_details->type;

            return view('admin.static_pages.edit')
                    ->with('page', 'static_pages')
                    ->with('sub_page', 'static_pages-view')
                    ->with('static_keys', array_unique($static_keys))
                    ->with('static_page_details', $static_page_details);
            
        } catch(Exception $e) {

            return redirect()->route('admin.static_pages.index')->with('flash_error' , $e->getMessage());

        }
    }

    /**
     * @method static_pages_save()
     *
     * @uses To save the page details of new/existing page object based on details
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
                'title' =>  !$request->static_page_id ? 'required|max:191|unique:static_pages,title' : 'required',
                'description' => 'required',
                'type' => !$request->static_page_id ? 'required' : ""
            ]; 
            
            Helper::custom_validator($request->all(), $rules);

            if($request->static_page_id != '') {

                $static_page_details = \App\StaticPage::find($request->static_page_id);

                $message = tr('static_page_updated_success');                    

            } else {

                $check_page = "";

                // Check the staic page already exists

                if($request->type != 'others') {

                    $check_page = \App\StaticPage::where('type',$request->type)->first();

                    if($check_page) {

                        return back()->with('flash_error',tr('static_page_already_alert'));
                    }

                }

                $message = tr('static_page_created_success');

                $static_page_details = new \App\StaticPage;

                $static_page_details->status = APPROVED;

            }

            $static_page_details->title = $request->title ?: $static_page_details->title;

            $static_page_details->description = $request->description ?: $static_page_details->description;

            $static_page_details->type = $request->type ?: $static_page_details->type;

            if($static_page_details->save()) {

                DB::commit();

                Helper::settings_generate_json();
                
                return redirect()->route('admin.static_pages.view', ['static_page_id' => $static_page_details->id] )->with('flash_success', $message);

            } 

            throw new Exception(tr('static_page_save_failed'), 101);
                      
        } catch(Exception $e) {

            DB::rollback();

            return back()->withInput()->with('flash_error', $e->getMessage());

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

            $static_page_details = \App\StaticPage::find($request->static_page_id);

            if(!$static_page_details) {

                throw new Exception(tr('static_page_not_found'), 101);
                
            }

            if($static_page_details->delete()) {

                DB::commit();

                return redirect()->route('admin.static_pages.index')->with('flash_success', tr('static_page_deleted_success')); 

            } 

            throw new Exception(tr('static_page_error'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.static_pages.index')->with('flash_error', $e->getMessage());

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

        $static_page_details = \App\StaticPage::find($request->static_page_id);

        if(!$static_page_details) {
           
            return redirect()->route('admin.static_pages.index')->with('flash_error',tr('static_page_not_found'));

        }

        return view('admin.static_pages.view')
                    ->with('page', 'static_pages')
                    ->with('sub_page', 'static_pages-view')
                    ->with('static_page_details', $static_page_details);
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

            $static_page_details = \App\StaticPage::find($request->static_page_id);

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

            return redirect()->back()->with('flash_error', $e->getMessage());

        }

    }

    
    
    /**
     * @method faqs_index()
     *
     * @uses To list out faq details 
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
    public function faqs_index() {
       
        $faqs = \App\Faq::orderBy('created_at','desc')->paginate($this->take);

        return view('admin.faqs.index')
                    ->with('main_page','faqs-crud')
                    ->with('page','faqs')
                    ->with('sub_page' , 'faqs-view')
                    ->with('faqs' , $faqs);
    }

    /**
     * @method faqs_create()
     *
     * @uses To create faq details
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
    public function faqs_create() {

        $faq_details = new \App\Faq;

        return view('admin.faqs.create')
                    ->with('main_page','faqs-crud')
                    ->with('page' , 'faqs')
                    ->with('sub_page','faqs-create')
                    ->with('faq_details', $faq_details);
                
    }

    /**
     * @method faqs_edit()
     *
     * @uses To display and update faqs details based on the faq id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Faq Id
     * 
     * @return redirect view page 
     *
     */
    public function faqs_edit(Request $request) {

        try {

            $faq_details = \App\Faq::find($request->faq_id);

            if(!$faq_details) { 

                throw new Exception(tr('faq_not_found'), 101);

            }
           
            return view('admin.faqs.edit')
                    ->with('main_page','faqs-crud')
                    ->with('page' , 'faqs')
                    ->with('sub_page','faqs-view')
                    ->with('faq_details' , $faq_details); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.faqs.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method faqs_save()
     *
     * @uses To save the faqs details of new/existing Faq object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - Faq Form Data
     *
     * @return success message
     *
     */
    public function faqs_save(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'question' => 'required',
                'answer' => 'required',
            
            ];

            Helper::custom_validator($request->all(),$rules);

            $faq_details = $request->faq_id ? \App\Faq::find($request->faq_id) : new \App\Faq;

            if(!$faq_details) {

                throw new Exception(tr('faq_not_found'), 101);
            }

            $faq_details->question = $request->question;

            $faq_details->answer = $request->answer;

            $faq_details->status = APPROVED;

            if($faq_details->save() ) {

                DB::commit();

                $message = $request->faq_id ? tr('faq_update_success')  : tr('faq_create_success');

                return redirect()->route('admin.faqs.view', ['faq_id' => $faq_details->id])->with('flash_success', $message);
            } 

            throw new Exception(tr('faq_saved_error') , 101);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());
        } 

    }

    /**
     * @method faqs_view()
     *
     * @uses view the faqs details based on faq id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - Faq Id
     * 
     * @return View page
     *
     */
    public function faqs_view(Request $request) {
       
        try {
      
            $faq_details = \App\Faq::find($request->faq_id);
            
            if(!$faq_details) { 

                throw new Exception(tr('faq_not_found'), 101);                
            }

            return view('admin.faqs.view')
                        ->with('main_page','faqs-crud')
                        ->with('page', 'faqs') 
                        ->with('sub_page','faqs-view') 
                        ->with('faq_details' , $faq_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method faqs_delete()
     *
     * @uses delete the faq details based on faq id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Faq Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function faqs_delete(Request $request) {

        try {

            DB::begintransaction();

            $faq_details = \App\Faq::find($request->faq_id);
            
            if(!$faq_details) {

                throw new Exception(tr('faq_not_found'), 101);                
            }

            if($faq_details->delete()) {

                DB::commit();

                return redirect()->route('admin.subscriptions.index')->with('flash_success',tr('faq_deleted_success'));   

            } 
            
            throw new Exception(tr('faq_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method faqs_status
     *
     * @uses To update faq status as DECLINED/APPROVED based on faqs id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Faq Id
     * 
     * @return response success/failure message
     *
     **/
    public function faqs_status(Request $request) {

        try {

            DB::beginTransaction();

            $faq_details = \App\Faq::find($request->faq_id);

            if(!$faq_details) {

                throw new Exception(tr('faq_not_found'), 101);
                
            }

            $faq_details->status = $faq_details->status ? DECLINED : APPROVED ;

            if($faq_details->save()) {

                DB::commit();

                $message = $faq_details->status ? tr('faq_approve_success') : tr('faq_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('faq_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.faqs.index')->with('flash_error', $e->getMessage());

        }

    }


}
