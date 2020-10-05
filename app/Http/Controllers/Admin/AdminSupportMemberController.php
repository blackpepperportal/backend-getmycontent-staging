<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

class AdminSupportMemberController extends Controller
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
     * @method support_members_index()
     *
     * @uses To list out support_members details 
     *
     * @created 
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function support_members_index(Request $request) {
        
        $base_query = \App\SupportMember::orderBy('created_at','desc');

        if($request->search_key) {

            $base_query = $base_query
                    ->where('support_members.name','LIKE','%'.$request->search_key.'%')
                    ->orWhere('support_members.email','LIKE','%'.$request->search_key.'%')
                    ->orWhere('support_members.mobile','LIKE','%'.$request->search_key.'%');
        }

        if($request->status) {

            switch ($request->status) {

                case SORT_BY_APPROVED:
                    $base_query = $base_query->where('support_members.status', SUPPORT_MEMBER_APPROVED);
                    break;

                case SORT_BY_DECLINED:
                    $base_query = $base_query->where('support_members.status', SUPPORT_MEMBER_DECLINED);
                    break;

                case SORT_BY_EMAIL_VERIFIED:
                    $base_query = $base_query->where('support_members.is_verified',SUPPORT_MEMBER_EMAIL_VERIFIED);
                    break;
                
                default:
                    $base_query = $base_query->where('support_members.is_verified',SUPPORT_MEMBER_EMAIL_NOT_VERIFIED);
                    break;
            }
        }
    
        $support_members = $base_query->paginate(10);

        return view('admin.support_members.index')
                    ->with('page', 'support_members')
                    ->with('sub_page', 'support_members-view')
                    ->with('support_members', $support_members);
    
    }

    /**
     * @method support_members_create()
     *
     * @uses To create support_member details
     *
     * @created  
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function support_members_create() {

        $support_member_details = new \App\SupportMember;

        return view('admin.support_members.create')
                    ->with('page', 'support_members')
                    ->with('sub_page','support_members-create')
                    ->with('support_member_details', $support_member_details);           
   
    }

    /**
     * @method support_members_edit()
     *
     * @uses To display and update support_member details based on the support_member id
     *
     * @created 
     *
     * @updated 
     *
     * @param object $request - support_member Id
     * 
     * @return redirect view page 
     *
     */
    public function support_members_edit(Request $request) {

        try {

            $support_member_details = \App\support_member::find($request->support_member_id);

            if(!$support_member_details) { 

                throw new Exception(tr('support_member_not_found'), 101);
            }

            return view('admin.support_members.edit')
                    ->with('page', 'support_members')
                    ->with('sub_page', 'support_members-view')
                    ->with('support_member_details', $support_member_details); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.support_members.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method support_members_save()
     *
     * @uses To save the support_members details of new/existing support_member object based on details
     *
     * @created 
     *
     * @updated 
     *
     * @param object request - support_member Form Data
     *
     * @return success message
     *
     */
    public function support_members_save(Request $request) {
        
        try {

            DB::begintransaction();

            $rules = [
                
                
                'first_name' => $request->support_member_id ?'required|max:191' :'',
                'last_name' => $request->support_member_id ?'required|max:191' :'',
                'email' => $request->support_member_id ? 'required|email|max:191|unique:support_members,email,'.$request->support_member_id.',id' : 'required|email|max:191|unique:support_members,email,NULL,id',
                'password' => $request->support_member_id ? "" : 'required|min:6|confirmed',
                'mobile' => $request->mobile ? 'digits_between:6,13' : '',
                'picture' => 'mimes:jpg,png,jpeg',
                'support_members_id' => 'exists:support_members,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $support_member_details = $request->support_members_id ? \App\SupportMember::find($request->support_member_id) : new \App\SupportMember;

            $is_new_support_member = NO;

            if($support_member_details->id) {

                $message = tr('support_member_updated_success'); 

            } else {

                $is_new_support_member = YES;

                $support_member_details->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('support_member_created_success');

                //$support_member_details->email_verified_at = date('Y-m-d H:i:s');

                $support_member_details->picture = asset('placeholder.jpeg');

                //$support_member_details->is_verified = EMAIL_VERIFIED;

                $support_member_details->token = Helper::generate_token();

                $support_member_details->token_expiry = Helper::generate_token_expiry();

            }

            $support_member_details->name = $request->first_name ?: $support_member_details->first_name;
            $support_member_details->first_name = $request->first_name ?: $support_member_details->first_name;
            $support_member_details->last_name = $request->last_name ?: $support_member_details->last_name;

            $support_member_details->email = $request->email ?: $support_member_details->email;

            $support_member_details->mobile = $request->mobile ?: '';

            //$support_member_details->login_by = $request->login_by ?: 'manual';
            
            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->support_member_id) {

                    Helper::storage_delete_file($support_member_details->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $support_member_details->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($support_member_details->save()) {

                if($is_new_support_member == YES) {

                    /**
                     * @todo Welcome mail notification
                     */

                    $email_data['subject'] = tr('support_member_welcome_email' , Setting::get('site_name'));

                    $email_data['email']  = $support_member_details->email;

                    $email_data['name'] = $support_member_details->first_name;

                    $email_data['page'] = "emails.support_members.welcome";

                    $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    //$support_member_details->is_verified = SUPPORT_MEMBER_EMAIL_VERIFIED;

                    $support_member_details->save();

                }

                DB::commit(); 

                return redirect(route('admin.support_members.view', ['support_member_id' => $support_member_details->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('support_member_save_failed'));
            
        } 
        catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method support_members_view()
     *
     * @uses Display the specified support_member details based on support_member_id
     *
     * @created  
     *
     * @updated 
     *
     * @param object $request - support_member Id
     * 
     * @return View page
     *
     */
    public function support_members_view(Request $request) {
       
        try {
      
            $support_member_details = \App\support_member::find($request->support_member_id);

            if(!$support_member_details) { 

                throw new Exception(tr('support_member_not_found'), 101);                
            }

            return view('admin.support_members.view')
                        ->with('page', 'support_members') 
                        ->with('sub_page','support_members-view') 
                        ->with('support_member_details' , $support_member_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method support_members_delete()
     *
     * @uses delete the support_member details based on support_member id
     *
     * @created  
     *
     * @updated  
     *
     * @param object $request - support_member Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function support_members_delete(Request $request) {

        try {

            DB::begintransaction();

            $support_member_details = \App\support_member::find($request->support_member_id);
            
            if(!$support_member_details) {

                throw new Exception(tr('support_member_not_found'), 101);                
            }

            if($support_member_details->delete()) {

                DB::commit();

                return redirect()->route('admin.support_members.index')->with('flash_success',tr('support_member_deleted_success'));   

            } 
            
            throw new Exception(tr('support_member_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method support_members_status
     *
     * @uses To update support_member status as DECLINED/APPROVED based on support_members id
     *
     * @created 
     *
     * @updated 
     *
     * @param object $request - support_member Id
     * 
     * @return response success/failure message
     *
     **/
    public function support_members_status(Request $request) {

        try {

            DB::beginTransaction();

            $support_member_details = \App\support_member::find($request->support_member_id);

            if(!$support_member_details) {

                throw new Exception(tr('support_member_not_found'), 101);
                
            }

            $support_member_details->status = $support_member_details->status ? DECLINED : APPROVED ;

            if($support_member_details->save()) {

                if($support_member_details->status == DECLINED) {

                    $email_data['subject'] = tr('support_member_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('support_member_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');

                }

                $email_data['email']  = $support_member_details->email;

                $email_data['name']  = $support_member_details->name;

                $email_data['page'] = "emails.support_members.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                $message = $support_member_details->status ? tr('support_member_approve_success') : tr('support_member_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('support_member_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.support_members.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method support_members_verify_status()
     *
     * @uses verify the support_member
     *
     * @created 
     *
     * @updated
     *
     * @param object $request - support_member Id
     *
     * @return redirect back page with status of the support_member verification
     */
    public function support_members_verify_status(Request $request) {

        try {

            DB::beginTransaction();

            $support_member_details = \App\support_member::find($request->support_member_id);

            if(!$support_member_details) {

                throw new Exception(tr('support_member_details_not_found'), 101);
                
            }

            $support_member_details->is_verified = $support_member_details->is_verified ? support_member_EMAIL_NOT_VERIFIED : support_member_EMAIL_VERIFIED;

            if($support_member_details->save()) {

                DB::commit();

                $message = $support_member_details->is_verified ? tr('support_member_verify_success') : tr('support_member_unverify_success');

                return redirect()->route('admin.support_members.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('support_member_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.support_members.index')->with('flash_error', $e->getMessage());

        }
    
    }

     /**
     * @method support_member_followers()
     *
     * @uses This is to display the all followers of specified content creator
     *
     * @created 
     *
     * @updated
     *
     * @param object $request - follower Id
     *
     * @return view page
     */
     public function support_member_followers(Request $request) {

        $support_member_followers = \App\Follower::where('follower_id',$request->follower_id)->paginate($this->take);
        
        return view('admin.support_members.followers')
                ->with('page','support_members')
                ->with('sub_page','support_members-view')
                ->with('support_member_followers',$support_member_followers);
     }

     /**
     * @method support_member_following()
     *
     * @uses This is to display the all followers of specified 
     *
     * @created 
     *
     * @updated
     *
     * @param object $request - follower Id
     *
     * @return view page
     */
     public function support_member_following(Request $request) {

        $support_member_followings = \App\Follower::where('support_member_id',$request->support_member_id)->paginate($this->take);

        return view('admin.support_members.following')
                ->with('page','support_members')
                ->with('sub_page','support_members-view')
                ->with('support_member_followings',$support_member_followings);
       
     }
}
