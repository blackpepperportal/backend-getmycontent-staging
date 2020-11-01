<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

class AdminAccountController extends Controller
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

        $admin = Auth::guard('admin')->user();

        return view('admin.account.profile')
                ->with('page', 'profile')
                ->with('admin', $admin);
    
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
            
            $admin = \App\Admin::find($request->admin_id);

            if(!$admin) {

                Auth::guard('admin')->logout();

                throw new Exception(tr('admin_not_found'), 101);
            }
        
            $admin->name = $request->name ?: $admin->name;

            $admin->email = $request->email ?: $admin->email;

            $admin->about = $request->about ?: $admin->about;
  
            if($request->hasFile('picture') ) {
                
                Helper::storage_delete_file($admin->picture, PROFILE_PATH_ADMIN); 
                
                $admin->picture = Helper::storage_upload_file($request->file('picture'), PROFILE_PATH_ADMIN);
            }
            
            $admin->remember_token = Helper::generate_token();

            $admin->save();

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

            $admin = \App\Admin::find(Auth::guard('admin')->user()->id);

            if(!$admin) {

                Auth::guard('admin')->logout();
                              
                throw new Exception(tr('admin_not_found'), 101);

            }

            if(Hash::check($request->old_password,$admin->password)) {

                $admin->password = Hash::make($request->password);

                $admin->save();

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


}
