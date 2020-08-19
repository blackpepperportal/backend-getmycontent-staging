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

    /**
     * @method post_payments()
     *
     * @uses Display the lists of post payments
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function post_payments(Request $request) {

        $base_query = \App\PostPayment::where('is_failed',NO);

        if($request->post_id) {

            $base_query = $base_query->where('post_id',$request->post_id);
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query
                            ->whereHas('userDetails',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })->orWhere('post_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $post_payments = $base_query->paginate(10);
       
        return view('admin.posts.payments')
                ->with('page','payments')
                ->with('sub_page','post-payments')
                ->with('post_payments',$post_payments);
    }


    /**
     * @method post_payments_view()
     *
     * @uses 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function post_payments_view(Request $request) {

        try {

            $post_payment_details = \App\PostPayment::where('id',$request->post_payment_id)->first();

            if(!$post_payment_details) {

                throw new Exception(tr('post_payment_not_found'), 1);
                
            }
           
            return view('admin.posts.payments_view')
                    ->with('page','payments')
                    ->with('sub_page','post-payments')
                    ->with('post_payment_details',$post_payment_details);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

    /**
     * @method order_payments()
     *
     * @uses Display the lists of order payments
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function order_payments(Request $request) {

        $base_query = \App\OrderPayment::where('status',APPROVED);

        if($request->order_id) {

            $base_query = $base_query->where('order_id',$request->order_id);
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query
                            ->whereHas('userDetails',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })->orWhere('order_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $order_payments = $base_query->paginate(10);
       
        return view('admin.orders.payments')
                ->with('page','payments')
                ->with('sub_page','order-payments')
                ->with('order_payments',$order_payments);
    }


    /**
     * @method order_payments_view()
     *
     * @uses Display the specified order payment details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function order_payments_view(Request $request) {

        try {

            $order_payment_details = \App\OrderPayment::where('id',$request->order_payment_id)->first();

            if(!$order_payment_details) {

                throw new Exception(tr('order_payment_not_found'), 1);
                
            }
           
            return view('admin.orders.payments_view')
                    ->with('page','payments')
                    ->with('sub_page','order-payments')
                    ->with('order_payment_details',$order_payment_details);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

    /**
     * @method revenue_dashboard()
     *
     * @uses Show the revenue dashboard.
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
    public function revenues_dashboard() {
        
        $data = new \stdClass;

        $data->order_payments = \App\OrderPayment::sum('total');

        $data->post_payments = \App\PostPayment::sum('paid_amount');

        $data->total_payments =  $data->order_payments + $data->post_payments;

        $order_today_payments = \App\OrderPayment::whereDate('paid_date',today())->sum('total');

        $post_today_payments = \App\PostPayment::whereDate('paid_date',today())->sum('paid_amount');

        $data->today_payments = $order_today_payments + $post_today_payments;
        
        return view('admin.revenues.dashboard')
                    ->with('page' , 'revenue-dashboard')
                    ->with('data', $data);
    
    }


}
