<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;


class AdminProductController extends Controller
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
     * @method user_products_index()
     *
     * @uses To list out stardom products details 
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
    public function user_products_index(Request $request) {

        $base_query = \App\UserProduct::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('userDetails',function($query) use($search_key) {

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('user_products.name','LIKE','%'.$search_key.'%');
        }

        if($request->user_id){

            $base_query = $base_query->where('user_id',$request->user_id);
        }
       
        $user_products = $base_query->paginate(10);

        return view('admin.user_products.index')
                ->with('page', 'user_products')
                ->with('sub_page' , 'user_products-view')
                ->with('user_products' , $user_products);
    }

    /**
     * @method user_products_create()
     *
     * @uses To create stardom product details
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
    public function user_products_create() {

        $user_product_details = new \App\UserProduct;

        $users = \App\User::where('is_content_creator', YES)->where('status', APPROVED)->get();

        return view('admin.user_products.create')
                ->with('page', 'user_products')
                ->with('sub_page', 'user_products-create')
                ->with('user_product_details', $user_product_details)
                ->with('users', $users);           
    }

    /**
     * @method user_products_edit()
     *
     * @uses To display and update stardom product details based on the stardom product id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return redirect view page 
     *
     */
    public function user_products_edit(Request $request) {

        try {

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(tr('user_product_not_found'), 101);
            }

            $users = \App\User::where('is_content_creator', YES)->where('status', APPROVED)->get();

            foreach ($users as $key => $user_details) {

                $user_details->is_selected = NO;

                if($user_product_details->user_id == $user_details->id){
                    
                    $user_details->is_selected = YES;
                }

            }
            return view('admin.user_products.edit')
                ->with('page' , 'user_products')
                ->with('sub_page', 'user_products-view')
                ->with('user_product_details', $user_product_details)
                ->with('users', $users); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.user_products.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method user_products_save()
     *
     * @uses To save the stardom products details of new/existing stardom product object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - Stardom Product Form Data
     *
     * @return success message
     *
     */
    public function user_products_save(Request $request) {
        
        try {
            
            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'quantity' => 'required|max:100',
                'price' => 'required|max:100',
                'picture' => 'mimes:jpg,png,jpeg',
                'discription' => 'max:199',
                'user_id' => 'required',
                'user_id' => 'exists:users,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = $request->user_product_id ? \App\UserProduct::find($request->user_product_id) : new \App\UserProduct;

            if($user_product_details->id) {

                $message = tr('user_product_updated_success'); 

            } else {

                $message = tr('user_product_created_success');

            }

            $user_product_details->user_id = $request->user_id ?: $user_product_details->user_id;

            $user_product_details->name = $request->name ?: $user_product_details->name;

            $user_product_details->quantity = $request->quantity ?: $user_product_details->quantity;

            $user_product_details->price = $request->price ?: '';

            $user_product_details->description = $request->description ?: '';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_product_id) {

                    Helper::storage_delete_file($user_product_details->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $user_product_details->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($user_product_details->save()) {

                DB::commit(); 

                return redirect(route('admin.user_products.view', ['user_product_id' => $user_product_details->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('user_product_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method user_products_view()
     *
     * @uses displays the specified user product details based on user product id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - user product Id
     * 
     * @return View page
     *
     */
    public function user_products_view(Request $request) {
       
        try {
      
            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(tr('user_product_not_found'), 101);                
            }

            return view('admin.user_products.view')
                    ->with('page', 'user_products') 
                    ->with('sub_page', 'user_products-view')
                    ->with('user_product_details', $user_product_details);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method user_products_delete()
     *
     * @uses delete the stardom product details based on stardom id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Stardom Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function user_products_delete(Request $request) {

        try {

            DB::begintransaction();

            $user_product_details = \App\UserProduct::find($request->user_product_id);
            
            if(!$user_product_details) {

                throw new Exception(tr('user_product_not_found'), 101);                
            }

            if($user_product_details->delete()) {

                DB::commit();

                return redirect()->route('admin.user_products.index')->with('flash_success',tr('user_product_deleted_success'));   

            } 
            
            throw new Exception(tr('user_product_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method user_products_status
     *
     * @uses To update stardom product status as DECLINED/APPROVED based on stardom product id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function user_products_status(Request $request) {

        try {

            DB::beginTransaction();

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) {

                throw new Exception(tr('user_product_not_found'), 101);
                
            }

            $user_product_details->status = $user_product_details->status ? DECLINED : APPROVED ;

            if($user_product_details->save()) {

                DB::commit();

                if($user_product_details->status == DECLINED) {

                    $email_data['subject'] = tr('product_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('product_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');
                }

                $email_data['email']  = $user_product_details->userDetails->email ?? "-";

                $email_data['name']  = $user_product_details->userDetails->name ?? "-";

                $email_data['product_name']  = $user_product_details->name;

                $email_data['page'] = "emails.products.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $user_product_details->status ? tr('user_product_approve_success') : tr('user_product_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_product_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.user_products.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method user_products_dashboard()
     *
     * @uses 
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
    public function user_products_dashboard(Request $request) {

        try {

            $user_product_details = \App\UserProduct::where('id',$request->user_product_id)->first();

            if(!$user_product_details) {

                throw new Exception(tr('user_product_details_not_found'), 101);
            }

            $data = new \stdClass;

            $data->total_orders = \App\OrderProduct::where('user_product_id',$user_product_details->id)->count();

            $data->today_orders = \App\OrderProduct::where('user_product_id',$user_product_details->id)->whereDate('created_at',today())->count();

            $order_products_ids =  \App\OrderProduct::where('user_product_id',$user_product_details->id)->pluck('order_id');

            $data->total_revenue = $order_products_ids->count() > 0 ? \App\OrderPayment::whereIn('order_id',[$order_products_ids])->sum('total') : 0;

            $data->today_revenue = count($order_products_ids) > 0 ? \App\OrderPayment::whereIn('order_id',[$order_products_ids])->where('created_at',today())->sum('total') : 0;

            $ids = count($order_products_ids)> 0 ? $order_products_ids : 0 ;
            
            $data->analytics = last_x_days_revenue(6,$ids);
           
            return view('admin.user_products.dashboard')
                        ->with('page','user_products')
                        ->with('sub_page' , 'user_products-view')
                        ->with('data', $data);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }


    /**
     * @method order_products
     *
     * @uses Display all orders based the product details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function order_products(Request $request) {

        try {

            DB::beginTransaction();

            $order_products = \App\OrderProduct::where('user_product_id',$request->user_product_id)->get();

            if(!$order_products) {

                throw new Exception(tr('user_product_not_found'), 101);
                
            }

            return view('admin.user_products.order_products')
                        ->with('page', 'user_products')
                        ->with('sub_page', 'user_products-view')
                        ->with('order_products', $order_products);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.user_products.index')->with('flash_error', $e->getMessage());

        }

    }

}