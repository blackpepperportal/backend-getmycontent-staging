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

        $data->total_content_creators = \App\User::isContentCreator(YES)->count();

        $data->total_posts = \App\Post::count();

        $data->total_revenue = \App\SubscriptionPayment::where('status', PAID)->sum('subscription_payments.amount');

        $data->recent_users= \App\User::orderBy('id' , 'desc')->skip($this->skip)->take(TAKE_COUNT)->get();

        $data->recent_content_creators=  \App\User::isContentCreator(YES)->orderBy('id' , 'desc')->skip($this->skip)->take(TAKE_COUNT)->get(); 

        $data->analytics = last_x_months_data(12);
        
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

        $data->analytics = revenue_graph(6);
        
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
                    ->with('page', 'subscriptions')
                    ->with('sub_page', 'subscriptions-view')
                    ->with('subscriptions', $subscriptions);
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
                    ->with('page' , 'subscriptions')
                    ->with('sub_page', 'subscriptions-create')
                    ->with('subscription_details', $subscription_details)
                    ->with('subscription_plan_types', $subscription_plan_types);           
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
                    ->with('page', 'subscriptions')
                    ->with('sub_page', 'subscriptions-view')
                    ->with('subscription_details', $subscription_details)
                    ->with('subscription_plan_types', $subscription_plan_types); 
            
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
                        ->with('page', 'subscriptions') 
                        ->with('sub_page', 'subscriptions-view') 
                        ->with('subscription_details', $subscription_details);
            
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
     * @method user_withdrawals
     *
     * @uses Display all stardom withdrawals
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

    public function user_withdrawals(Request $request) {

        $base_query = \App\UserWithdrawal::orderBy('user_withdrawals.id', 'desc');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('userDetails',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('user_withdrawals.payment_id','LIKE','%'.$search_key.'%');
        }

        if($request->status) {

            $base_query = $base_query->where('user_withdrawals.status',$request->status);
        }

        $user_withdrawals = $base_query->paginate(10);
       
        return view('admin.user_withdrawals.index')
                ->with('page', 'content_creator-withdrawals')
                ->with('user_withdrawals', $user_withdrawals);

    }


    /**
     * @method user_withdrawals_view
     *
     * @uses Display all stardom specified 
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

    public function user_withdrawals_view(Request $request) {

          try {

            $user_withdrawal_details = \App\UserWithdrawal::where('id',$request->user_withdrawal_id)->first();


            if(!$user_withdrawal_details) { 

                throw new Exception(tr('user_withdrawal_not_found'), 101);                
            }  

            $billing_account_details = \App\UserBillingAccount::where('user_id', $user_withdrawal_details->user_id)->first();
       
            return view('admin.user_withdrawals.view')
                ->with('page', 'content_creator-withdrawals')
                ->with('user_withdrawal_details', $user_withdrawal_details)
                ->with('billing_account_details',$billing_account_details);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());

        }

    }

     /**
     * @method user_withdrawals_paynow()
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
    public function user_withdrawals_paynow(Request $request) {

        try {

            DB::begintransaction();

            $user_withdrawal_details = \App\UserWithdrawal::find($request->user_withdrawal_id);

            if(!$user_withdrawal_details) {

                throw new Exception(tr('user_withdrawal_details_not_found'),101);
                
            }

            $user_withdrawal_details->paid_amount = $user_withdrawal_details->requested_amount;

            $user_withdrawal_details->status = WITHDRAW_PAID;
            
            if($user_withdrawal_details->save()) {

                DB::commit();

                $email_data['subject'] = Setting::get('site_name');

                $email_data['page'] = "emails.users.withdrawals-approve";
    
                $email_data['data'] = $user_withdrawal_details->userDetails;
    
                $email_data['email'] = $user_withdrawal_details->userDetails->email ?? '';

                $email_data['message'] = tr('user_withdraw_paid_description');

                dispatch(new SendEmailJob($email_data));

                return redirect()->back()->with('flash_success',tr('payment_success'));
            }

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method user_withdrawals_reject()
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
    public function user_withdrawals_reject(Request $request) {

        try {

            DB::begintransaction();

            $user_withdrawal_details = \App\UserWithdrawal::find($request->user_withdrawal_id);

            if(!$user_withdrawal_details) {

                throw new Exception(tr('user_withdrawal_details_not_found'),101);
                
            }
            
            $user_withdrawal_details->status = WITHDRAW_REJECTED;
            
            if($user_withdrawal_details->save()) {

                DB::commit();

                $email_data['subject'] = Setting::get('site_name');

                $email_data['page'] = "emails.users.withdrawals-decline";
    
                $email_data['data'] = $user_withdrawal_details->userDetails;
    
                $email_data['email'] = $user_withdrawal_details->userDetails->email ?? '';

                $email_data['message'] = tr('user_withdraw_decline_description');

                dispatch(new SendEmailJob($email_data));

                return redirect()->back()->with('flash_success',tr('user_withdrawal_cancelled'));
            }

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }
    
    }

     /**
     * @method product_inventories_index()
     *
     * @uses Display the total inventory
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function product_inventories_index(Request $request) {

        $base_query = \App\ProductInventory::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query

                ->whereHas('userProductDetails', function($q) use ($search_key) {

                    return $q->Where('user_products.name','LIKE','%'.$search_key.'%');

                });
                        
        }

        if($request->user_product_id) {

            $base_query = $base_query->where('user_product_id',$request->user_product_id);
        }

        $product_inventories = $base_query->paginate(10);

        return view('admin.user_products.inventories.index')
                    ->with('page','product-inventories')
                    ->with('product_inventories' , $product_inventories);
    }

    /**
     * @method product_inventories_view()
     *
     * @uses Display the product inventories based on the product inentory id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - product inventory Id
     * 
     * @return View page
     *
     */
    public function product_inventories_view(Request $request) {
       
        try {
      
            $product_inventory_details = \App\ProductInventory::find($request->product_inventory_id);

            if(!$product_inventory_details) { 

                throw new Exception(tr('product_inventory_not_found'), 101);                
            }
        
            return view('admin.user_products.inventories.view')
                        ->with('page', 'posts') 
                        ->with('product_inventory_details',$product_inventory_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method subscription_payments_index()
     *
     * @uses Display the lists of subscriptions payments
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function subscription_payments_index(Request $request) {

        $base_query = \App\SubscriptionPayment::orderBy('created_at','desc');

        if($request->subscription_id) {

            $base_query = $base_query->where('subscription_id',$request->subscription_id);
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query
                            ->whereHas('userDetails',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                                
                            })->orWhere('subscription_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $subscription_payments = $base_query->paginate(10);
       
        return view('admin.revenues.subscription_payments.index')
                ->with('page','revenuse')
                ->with('sub_page','subscription-payments')
                ->with('subscription_payments',$subscription_payments);
    }


    /**
     * @method subscription_payments_view()
     *
     * @uses Display the subscription payment details for the users
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function subscription_payments_view(Request $request) {

        try {

            $subscription_payment_details = \App\SubscriptionPayment::where('id',$request->subscription_payment_id)->first();
           
            if(!$subscription_payment_details) {

                throw new Exception(tr('subscription_payment_details_not_found'), 1);
                
            }
           
            return view('admin.revenues.subscription_payments.view')
                    ->with('page','revenues')
                    ->with('sub_page','subscription-payments')
                    ->with('subscription_payment_details',$subscription_payment_details);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

}
