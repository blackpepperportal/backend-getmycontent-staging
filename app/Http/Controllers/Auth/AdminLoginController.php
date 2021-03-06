<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Auth;

use App\Admin;

class AdminLoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    /**
     * Show the application’s login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    protected function guard() {

        return Auth::guard('admin');

    }
    
    public function login(Request $request) {
       
        // Validate the form data
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:5'
         ]);
        // Attempt to log the user in
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
           
            if((Auth::guard('admin')->user()->is_sub_admin == YES) && (Auth::guard('admin')->user()->status) == DECLINED) {

                \Session::flash('flash_error', tr('sub_admin_account_decline_note'));
                
                Auth::guard('admin')->logout();

                return redirect()->route('admin.login')->with('flash_error', tr('username_password_not_match'));
            }

            // if successful, then redirect to their intended location
            return redirect()->route('admin.dashboard')->with('flash_success',tr('login_success'));

        }
     
        // if unsuccessful, then redirect back to the login with the form data
     
        return redirect()->back()->with('flash_error', tr('username_password_not_match'));
    }

    public function logout() {

        Auth::guard('admin')->logout();
        
        return redirect()->route('admin.login')->with('flash_success',tr('logout_success'));
    }

}