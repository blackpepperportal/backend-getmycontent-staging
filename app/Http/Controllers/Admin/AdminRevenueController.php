<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

use Carbon\Carbon;

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

        $data->total_premium_users = \App\User::where('user_account_type', USER_PREMIUM_ACCOUNT)->count();

        $data->total_posts = \App\Post::count();

        $data->total_revenue = \App\UserSubscriptionPayment::where('status', PAID)->sum('user_subscription_payments.amount');

        $data->recent_users= \App\User::orderBy('id' , 'desc')->skip($this->skip)->take(TAKE_COUNT)->get();

        $data->recent_premium_users = \App\User::where('user_account_type', USER_PREMIUM_ACCOUNT)->orderBy('id' , 'desc')->skip($this->skip)->take(TAKE_COUNT)->get(); 

        $data->analytics = last_x_months_data(12);

        $data->posts_data = last_x_months_posts(12) ?? [];

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
                            ->whereHas('user',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                                
                            })
                            ->orwhereHas('postDetails',function($query) use($search_key){

                                return $query->where('posts.unique_id','LIKE','%'.$search_key.'%');
                            })
                            ->orWhere('post_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $user = \App\User::find($request->user_id) ?? '';

        if($request->user_id) {

            $base_query  = $base_query->where('user_id',$request->user_id);
        }

        $post_payments = $base_query->orderBy('created_at','DESC')->paginate(10);
       
        return view('admin.posts.payments')
                ->with('page','payments')
                ->with('sub_page','post-payments')
                ->with('user',$user)
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

            $post_payment = \App\PostPayment::where('id',$request->post_payment_id)->first();

            if(!$post_payment) {

                throw new Exception(tr('post_payment_not_found'), 1);
                
            }
           
            return view('admin.posts.payments_view')
                    ->with('page','payments')
                    ->with('sub_page','post-payments')
                    ->with('post_payment',$post_payment);

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
                            ->whereHas('user',function($query) use($search_key){

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

            $order_payment = \App\OrderPayment::where('id',$request->order_payment_id)->first();

            if(!$order_payment) {

                throw new Exception(tr('order_payment_not_found'), 1);
                
            }
           
            return view('admin.orders.payments_view')
                    ->with('page','payments')
                    ->with('sub_page','order-payments')
                    ->with('order_payment',$order_payment);

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

        $data->subscription_payments = \App\UserSubscriptionPayment::where('status',PAID)->sum('amount');

        $data->total_payments =  $data->order_payments + $data->post_payments + $data->subscription_payments;

        $order_today_payments = \App\OrderPayment::where('status',PAID)->whereDate('paid_date',today())->sum('total');

        $post_today_payments = \App\PostPayment::where('status',PAID)->whereDate('paid_date',today())->sum('paid_amount');

        $subscription_today_payments = \App\SubscriptionPayment::where('status',PAID)->whereDate('paid_date',today())->sum('amount');

        $data->today_payments = $order_today_payments + $post_today_payments + $subscription_today_payments;

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

        $subscription = new \App\Subscription;

        $subscription_plan_types = [PLAN_TYPE_MONTH,PLAN_TYPE_YEAR,PLAN_TYPE_WEEK,PLAN_TYPE_DAY];

        return view('admin.subscriptions.create')
                    ->with('page' , 'subscriptions')
                    ->with('sub_page', 'subscriptions-create')
                    ->with('subscription', $subscription)
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

            $subscription = \App\Subscription::find($request->subscription_id);

            if(!$subscription) { 

                throw new Exception(tr('subscrprion_not_found'), 101);
            }

            $subscription_plan_types = [PLAN_TYPE_MONTH,PLAN_TYPE_YEAR,PLAN_TYPE_WEEK,PLAN_TYPE_DAY];
           
            return view('admin.subscriptions.edit')
                    ->with('page', 'subscriptions')
                    ->with('sub_page', 'subscriptions-view')
                    ->with('subscription', $subscription)
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

            $subscription = $request->subscription_id ? \App\Subscription::find($request->subscription_id) : new \App\Subscription;

            if(!$subscription) {

                throw new Exception(tr('subscription_not_found'), 101);
            }

            $subscription->title = $request->title;

            $subscription->description = $request->description ?: "";

            $subscription->plan = $request->plan;

            $subscription->plan_type = $request->plan_type;

            $subscription->amount = $request->amount;

            $subscription->is_free = $request->is_free == YES ? YES :NO;
        
            $subscription->is_popular  = $request->is_popular == YES ? YES :NO;

            if( $subscription->save() ) {

                DB::commit();

                $message = $request->subscription_id ? tr('subscription_update_success')  : tr('subscription_create_success');

                return redirect()->route('admin.subscriptions.view', ['subscription_id' => $subscription->id])->with('flash_success', $message);
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
      
            $subscription = \App\Subscription::find($request->subscription_id);
            
            if(!$subscription) { 

                throw new Exception(tr('subscription_not_found'), 101);                
            }

            return view('admin.subscriptions.view')
                        ->with('page', 'subscriptions') 
                        ->with('sub_page', 'subscriptions-view') 
                        ->with('subscription', $subscription);
            
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

            $subscription = \App\Subscription::find($request->subscription_id);
            
            if(!$subscription) {

                throw new Exception(tr('subscription_not_found'), 101);                
            }

            if($subscription->delete()) {

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

            $subscription = \App\Subscription::find($request->subscription_id);

            if(!$subscription) {

                throw new Exception(tr('subscription_not_found'), 101);
                
            }

            $subscription->status = $subscription->status ? DECLINED : APPROVED ;

            if($subscription->save()) {

                DB::commit();

                $message = $subscription->status ? tr('subscription_approve_success') : tr('subscription_decline_success');

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

            $base_query = $base_query->whereHas('user',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('user_withdrawals.payment_id','LIKE','%'.$search_key.'%');
        }

        if($request->has('status')) {

            $base_query = $base_query->where('user_withdrawals.status',$request->status);
        }


        if($request->user_id) {

            $base_query = $base_query->where('user_withdrawals.user_id',$request->user_id);
        }


        $user = \App\User::find($request->user_id)??'';

        $user_withdrawals = $base_query->paginate($this->take);
       
        return view('admin.user_withdrawals.index')
                ->with('page', 'content_creator-withdrawals')
                ->with('user', $user)
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

            $user_withdrawal = \App\UserWithdrawal::where('id',$request->user_withdrawal_id)->first();


            if(!$user_withdrawal) { 

                throw new Exception(tr('user_withdrawal_not_found'), 101);                
            }  

            $billing_account_details = \App\UserBillingAccount::where('user_id', $user_withdrawal->user_id)->first();
       
            return view('admin.user_withdrawals.view')
                ->with('page', 'content_creator-withdrawals')
                ->with('user_withdrawal', $user_withdrawal)
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

            $user_withdrawal = \App\UserWithdrawal::find($request->user_withdrawal_id);

            if(!$user_withdrawal) {

                throw new Exception(tr('user_withdrawal_not_found'),101);
                
            }

            $user_withdrawal->paid_amount = $user_withdrawal->requested_amount;

            $user_withdrawal->status = WITHDRAW_PAID;
            
            if($user_withdrawal->save()) {

                DB::commit();

                $email_data['subject'] = Setting::get('site_name');

                $email_data['page'] = "emails.users.withdrawals-approve";
    
                $email_data['data'] = $user_withdrawal->user;
    
                $email_data['email'] = $user_withdrawal->user->email ?? '';

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

            $user_withdrawal = \App\UserWithdrawal::find($request->user_withdrawal_id);

            if(!$user_withdrawal) {

                throw new Exception(tr('user_withdrawal_not_found'),101);
                
            }
            
            $user_withdrawal->status = WITHDRAW_DECLINED;
            
            if($user_withdrawal->save()) {

                DB::commit();

                $email_data['subject'] = Setting::get('site_name');

                $email_data['page'] = "emails.users.withdrawals-decline";
    
                $email_data['data'] = $user_withdrawal->user;
    
                $email_data['email'] = $user_withdrawal->user->email ?? '';

                $email_data['message'] = tr('user_withdraw_decline_description');

                dispatch(new SendEmailJob($email_data));

                return redirect()->back()->with('flash_success',tr('user_withdrawal_rejected'));
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
      
            $product_inventory = \App\ProductInventory::find($request->product_inventory_id);

            if(!$product_inventory) { 

                throw new Exception(tr('product_inventory_not_found'), 101);                
            }
        
            return view('admin.user_products.inventories.view')
                        ->with('page', 'posts') 
                        ->with('product_inventory',$product_inventory);
            
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
                            ->whereHas('user',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                                
                            })->orWhere('subscription_payments.payment_id','LIKE','%'.$search_key.'%');
        }


        if($request->today_revenue){

            $base_query = $base_query->whereDate('subscription_payments.created_at', Carbon::today());

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

            $subscription_payment = \App\SubscriptionPayment::where('id',$request->subscription_payment_id)->first();
           
            if(!$subscription_payment) {

                throw new Exception(tr('subscription_payment_not_found'), 1);
                
            }
           
            return view('admin.revenues.subscription_payments.view')
                    ->with('page','revenues')
                    ->with('sub_page','subscription-payments')
                    ->with('subscription_payment',$subscription_payment);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }


    /**
     * @method user_wallets_index()
     *
     * @uses Display the lists of stardom users
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function user_wallets_index(Request $request) {

        $base_query = \App\UserWallet::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query

                ->whereHas('user', function($q) use ($search_key) {

                    return $q->Where('users.name','LIKE','%'.$search_key.'%');

                })->orWhere('user_wallets.unique_id','LIKE','%'.$search_key.'%');
                        
        }

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        $user_wallets = $base_query->paginate(10);

        return view('admin.user_wallets.index')
                    ->with('page','user_wallets')
                    ->with('user_wallets' , $user_wallets);
    }

    /**
     * @method user_wallets_view()
     *
     * @uses display the transaction details of the perticulor stardom
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - stardom_wallet_id
     * 
     * @return View page
     *
     */
    public function user_wallets_view(Request $request) {
       
        try {
            
            $user_wallet = \App\UserWallet::where('user_id',$request->user_id)->first();
           
            if(!$user_wallet) { 

                $user_wallet = new \App\UserWallet;

                $user_wallet->user_id = $request->user_id;

                $user_wallet->save();

            }

            $user_wallet_payments = \App\UserWalletPayment::where('requested_amount','>',0)->where('user_id',$user_wallet->user_id)->paginate(10);
                   
            return view('admin.user_wallets.view')
                        ->with('page', 'user_wallets') 
                        ->with('user_wallet', $user_wallet)
                        ->with('user_wallet_payments', $user_wallet_payments);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }


     /**
     * @method subscription_payments_send_invoice
     *
     * @uses to send user invoice request details based on request id
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     * 
     * @return view page
     *
     **/
    
    public function subscription_payments_send_invoice(Request $request) {

        try {

            $subscription_payment = \App\UserSubscriptionPayment::where('id', $request->user_subscription_id)->first();
            
            if(!$subscription_payment) {

                throw new Exception(tr('subscription_payment_not_found'), 101);
            }

            $user = \App\User::find($subscription_payment->from_user_id);
            
            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
            }



            $to_user  = \App\User::find($subscription_payment->to_user_id);

            $email_data = [];

            $email_data['timezone'] =  Auth::guard('admin')->user()->timezone ?? "";

            $email_data['subscription_payments'] =  $subscription_payment ?? "";

            $email_data['user'] = $user ?? '';

            $email_data['subscriptions'] = $subscription_payment->userSubscription ?? '';

            $email_data['subject'] =  tr('subscription_invoice_message')." ".Setting::get('site_name');

            $email_data['message'] =  tr('user_subscription_message',$subscription_payment->plan_text_formatted)." ".$to_user->name;
            
            $email_data['page'] = "emails.users.subscription-invoice";

            $email_data['email'] = $user->email ?? '';

            $email_data['data'] = $email_data;

            $email_data['is_invoice'] = 1;

            $email_data['filename'] = 'Invoice'.date('m-d-Y_hia').'.pdf';


            $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

            return redirect()->back()->with('flash_success',tr('invoice_mail_sent_success'));

        } catch(Exception $e) {

            return redirect()->route('admin.user_subscriptions.index')->with('flash_error', $e->getMessage());

        }

    }

}
