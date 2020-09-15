<?php

namespace App\Http\Controllers\SupportMember;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

class SupportMemberRevenueController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

        $this->middleware('auth:support_member');

        $this->skip = $request->skip ?: 0;
       
        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    public function dashboard() {

    	return view('support_member.dashboard');
    }
}
