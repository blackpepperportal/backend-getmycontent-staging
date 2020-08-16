<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

class AdminRevenueController extends Controller
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
     * @method main_dashboard()
     *
     * @uses Show the application dashboard.
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function main_dashboard() {
        
        $data = new \stdClass;

        $data->total_users = \App\User::count();

        return view('admin.dashboard')
                    ->with('page' , 'dashboard')
                    ->with('data', $data);
    
    }

}
