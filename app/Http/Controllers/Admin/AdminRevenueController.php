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

        if($request->user_id) {

            $base_query  = $base_query->where('user_id',$request->user_id);
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

        $data->order_payments = \App\OrderPayment::where('status',PAID)->sum('total');

        $data->post_payments = \App\PostPayment::where('status',PAID)->sum('paid_amount');

        $data->total_payments =  $data->order_payments + $data->post_payments;

        $order_today_payments = \App\OrderPayment::where('status',PAID)->whereDate('paid_date',today())->sum('total');

        $post_today_payments = \App\PostPayment::where('status',PAID)->whereDate('paid_date',today())->sum('paid_amount');

        $data->today_payments = $order_today_payments + $post_today_payments;

        $data->analytics = last_x_days_revenue(6);
        
        return view('admin.revenues.dashboard')
                    ->with('page' , 'revenue-dashboard')
                    ->with('data', $data);
    
    }


    /**
     * @method subscriptions_index()
     *
     * @uses To list out subscription details 
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
    public function subscriptions_index() {
       
        $subscriptions = \App\Subscription::orderBy('updated_at','desc')->paginate(10);

        return view('admin.subscriptions.index')
                    ->with('main_page','subscriptions-crud')
                    ->with('page','subscriptions')
                    ->with('sub_page' , 'subscriptions-view')
                    ->with('subscriptions' , $subscriptions);
    }

    /**
     * @method subscriptions_create()
     *
     * @uses To create subscriptions details
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
    public function subscriptions_create() {

        $subscription_details = new \App\Subscription;

        $subscription_plan_types = [PLAN_TYPE_MONTH,PLAN_TYPE_YEAR,PLAN_TYPE_WEEK,PLAN_TYPE_DAY];

        return view('admin.subscriptions.create')
                    ->with('main_page','subscriptions-crud')
                    ->with('page' , 'subscriptions')
                    ->with('sub_page','subscriptions-create')
                    ->with('subscription_details', $subscription_details)
                    ->with('subscription_plan_types',$subscription_plan_types);           
    }

    /**
     * @method subscriptions_edit()
     *
     * @uses To display and update subscriptions details based on the instructor id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Subscription Id
     * 
     * @return redirect view page 
     *
     */
    public function subscriptions_edit(Request $request) {

        try {

            $subscription_details = \App\Subscription::find($request->subscription_id);

            if(!$subscription_details) { 

                throw new Exception(tr('subscrprion_not_found'), 101);
            }

            $subscription_plan_types = [PLAN_TYPE_MONTH,PLAN_TYPE_YEAR,PLAN_TYPE_WEEK,PLAN_TYPE_DAY];
           
            return view('admin.subscriptions.edit')
                    ->with('main_page','subscriptions-crud')
                    ->with('page' , 'subscriptions')
                    ->with('sub_page','subscriptions-view')
                    ->with('subscription_details' , $subscription_details)
                    ->with('subscription_plan_types',$subscription_plan_types); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.subscriptions.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method subscriptions_save()
     *
     * @uses To save the subscriptions details of new/existing subscription object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - Subscrition Form Data
     *
     * @return success message
     *
     */
    public function subscriptions_save(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'title'  => 'required|max:255',
                'description' => 'max:255',
                'amount' => 'required|numeric|min:0|max:10000000',
                'plan' => 'required',
                'plan_type' => 'required',
            
            ];

            Helper::custom_validator($request->all(),$rules);

            $subscription_details = $request->subscription_id ? \App\Subscription::find($request->subscription_id) : new \App\Subscription;

            if(!$subscription_details) {

                throw new Exception(tr('subscription_not_found'), 101);
            }

            $subscription_details->status = APPROVED;

            $subscription_details->title = $request->title;

            $subscription_details->description = $request->description ?: "";

            $subscription_details->plan = $request->plan;

            $subscription_details->plan_type = $request->plan_type;

            $subscription_details->amount = $request->amount;

            $subscription_details->is_free = $request->is_free == YES ? YES :NO;
        
            $subscription_details->is_popular  = $request->is_popular == YES ? YES :NO;

            if( $subscription_details->save() ) {

                DB::commit();

                $message = $request->subscription_id ? tr('subscription_update_success')  : tr('subscription_create_success');

                return redirect()->route('admin.subscriptions.view', ['subscription_id' => $subscription_details->id])->with('flash_success', $message);
            } 

            throw new Exception(tr('subscription_saved_error') , 101);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());
        } 

    }

    /**
     * @method subscriptions_view()
     *
     * @uses view the subscriptions details based on subscriptions id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - Subscription Id
     * 
     * @return View page
     *
     */
    public function subscriptions_view(Request $request) {
       
        try {
      
            $subscription_details = \App\Subscription::find($request->subscription_id);
            
            if(!$subscription_details) { 

                throw new Exception(tr('subscription_not_found'), 101);                
            }

            return view('admin.subscriptions.view')
                        ->with('main_page','subscriptions-crud')
                        ->with('page', 'subscriptions') 
                        ->with('sub_page','subscriptions-view') 
                        ->with('subscription_details' , $subscription_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method subscriptions_delete()
     *
     * @uses delete the subscription details based on subscription id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Subscription Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function subscriptions_delete(Request $request) {

        try {

            DB::begintransaction();

            $subscription_details = \App\Subscription::find($request->subscription_id);
            
            if(!$subscription_details) {

                throw new Exception(tr('subscription_not_found'), 101);                
            }

            if($subscription_details->delete()) {

                DB::commit();

                return redirect()->route('admin.subscriptions.index')->with('flash_success',tr('subscription_deleted_success'));   

            } 
            
            throw new Exception(tr('subscription_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method subscriptions_status
     *
     * @uses To update subscription status as DECLINED/APPROVED based on subscriptions id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Subscription Id
     * 
     * @return response success/failure message
     *
     **/
    public function subscriptions_status(Request $request) {

        try {

            DB::beginTransaction();

            $subscription_details = \App\Subscription::find($request->subscription_id);

            if(!$subscription_details) {

                throw new Exception(tr('subscription_not_found'), 101);
                
            }

            $subscription_details->status = $subscription_details->status ? DECLINED : APPROVED ;

            if($subscription_details->save()) {

                DB::commit();

                $message = $subscription_details->status ? tr('subscription_approve_success') : tr('subscription_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('subscription_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.subscriptions.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method stardom_withdrawals
     *
     * @uses To update subscription status as DECLINED/APPROVED based on subscriptions id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Subscription Id
     * 
     * @return response success/failure message
     *
     **/

    public function stardom_withdrawals(Request $request) {

        $base_query = \App\StardomWithDrawal::where('unique_id','!=',NULL);

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('stardomDetails',function($query) use($search_key){

                return $query->where('stardoms.name','LIKE','%'.$search_key.'%');

            })->orWhere('stardom_with_drawals.payment_id','LIKE','%'.$search_key.'%');
        }

        if($request->status) {

            $base_query = $base_query->where('stardom_with_drawals.status',$request->status);
        }

        $stardom_withdrawals = $base_query->paginate(10);

        return view('admin.stardom_withdrawals.index')
                ->with('page','stardom-withdrawls')
                ->with('stardom_withdrawals',$stardom_withdrawals);

    }

     /**
     * @method stardom_withdrawals_payment()
     *
     * @uses 
     *
     * @created Akshata
     *
     * @updated
     *
     * @param Integer $request - stardom withdrawal id
     * 
     * @return view page
     *
     **/
    public function stardom_withdrawals_payment(Request $request) {

        try {

            DB::begintransaction();

            $rules =  [
                'amount' => 'required|numeric|gt:0',
            ]; 
            
            Helper::custom_validator($request->all(), $rules);

            $stardom_withdrawal_details = \App\StardomWithDrawal::find($request->stardom_withdrawal_id);

            if(!$stardom_withdrawal_details) {

                throw new Exception(tr('stardom_withdrawal_details_not_found'),101);
                
            }

            if($stardom_withdrawal_details->requested_amount < $request->amount) {

                throw new Exception(tr('amount_is_greater_than_requested_amount'),101);
                
            }

            $stardom_withdrawal_details->paid_amount += $request->amount;

            $stardom_withdrawal_details->status = PAID;
            
            if($stardom_withdrawal_details->save()) {

                DB::commit();

                return redirect()->back()->with('flash_success',tr('payment_success'));
            }

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return redirect()->back()->with('flash_error', $error);

        }
    
    }

    /**
     * @method stardom_withdrawals_reject()
     *
     * @uses 
     *
     * @created Akshata
     *
     * @updated
     *
     * @param Integer $request - stardom withdrawal id
     * 
     * @return view page
     *
     **/
    public function stardom_withdrawals_reject(Request $request) {

        try {

            DB::begintransaction();

            $stardom_withdrawal_details = \App\StardomWithDrawal::find($request->stardom_withdrawal_id);

            if(!$stardom_withdrawal_details) {

                throw new Exception(tr('stardom_withdrawal_details_not_found'),101);
                
            }
            
            $stardom_withdrawal_details->status = PAYMENT_REJECTED;
            
            if($stardom_withdrawal_details->save()) {

                DB::commit();

                return redirect()->back()->with('flash_success',tr('stardom_withdrawal_cancelled'));
            }

        } catch(Exception $e) {

            DB::rollback();

            $error = $e->getMessage();

            return redirect()->back()->with('flash_error', $error);

        }
    
    }

}
