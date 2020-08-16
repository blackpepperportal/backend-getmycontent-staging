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
                    ->with('main_page','documents-crud')
                    ->with('page','documents')
                    ->with('sub_page' , 'documents-view')
                    ->with('documents' , $documents);
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
                    ->with('main_page','documents-crud')
                    ->with('page' , 'documents')
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
                    ->with('main_page','documents-crud')
                    ->with('page' , 'documents')
                    ->with('sub_page','documents-view')
                    ->with('document_details' , $document_details); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.documents.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method documents_save()
     *
     * @uses To save the document details of new/existing stardom object based on details
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

                if($request->stardom_id) {

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
                        ->with('main_page','documents-crud')
                        ->with('page', 'documents') 
                        ->with('sub_page','documents-view') 
                        ->with('document_details' , $document_details);
            
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

        $section_types = static_page_footers(0, $is_list = YES);

        $static_keys[] = 'others';

        $static_page_details = new \App\StaticPage;

        return view('admin.static_pages.create')
                ->with('page', 'static_pages')
                ->with('sub_page', 'static_pages-create')
                ->with('static_keys', $static_keys)
                ->with('static_page_details', $static_page_details)
                ->with('section_types', $section_types);
   
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

            $section_types = static_page_footers(0, $is_list = YES);

            $static_keys[] = 'others';

            $static_keys[] = $static_page_details->type;

            return view('admin.static_pages.edit')
                    ->with('page' , 'static_pages')
                    ->with('sub_page', 'static_pages-view')
                    ->with('static_keys', array_unique($static_keys))
                    ->with('static_page_details', $static_page_details)
                    ->with('section_types', $section_types);
            
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
                'title' => 'required|max:191',
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

}
