<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\User;

class UserProductApiController extends Controller
{
 	protected $loginUser;

    protected $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

	/**
     * @method user_products_index()
     *
     * @uses To list out stardom products details 
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param 
     * 
     * @return JSON Response
     *
     */
    public function user_products_index(Request $request) {

    	try {

            $base_query = $total_query = \App\UserProduct::where('user_id', $request->id);

            $user_products = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

	        $data['user_products'] = $user_products ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method user_products_save()
     *
     * @uses To save the stardom products details of new/existing
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object request - Stardom Product Form Data
     *
     * @return JSON Response
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
                'description' => 'max:199',
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'required|exists:sub_categories,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = \App\UserProduct::find($request->user_product_id) ?? new \App\UserProduct;

            $success_code = $user_product_details->id ? 122 : 121;

            $user_product_details->user_id = $request->id;

            $user_product_details->name = $request->name ?: $user_product_details->name;

            $user_product_details->quantity = $request->quantity ?: $user_product_details->quantity;

            $user_product_details->price = $request->price ?: '';

            $user_product_details->category_id = $request->category_id ?: $user_product_details->category_id;

            $user_product_details->sub_category_id = $request->sub_category_id ?: $user_product_details->sub_category_id;

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

                $data = \App\UserProduct::find($user_product_details->id);

                return $this->sendResponse(api_success($success_code), $success_code, $data);

            } 

            throw new Exception(api_error(130), 130);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 

    }

    /**
     * @method user_products_view()
     *
     * @uses displays the specified user product details based on user product id
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - user product Id
     * 
     * @return JSON Response
     *
     */
    public function user_products_view(Request $request) {
       
        try {
      	
      		$rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(api_error(133), 133);                
            }

            $data['user_product_details'] = $user_product_details;

            return $this->sendResponse($message = "", $success_code = "", $data);
            
        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method user_products_delete()
     *
     * @uses delete the stardom product details based on stardom id
     *
     * @created Bhawya
     *
     * @updated  
     *
     * @param object $request - Stardom Id
     * 
     * @return response of details
     *
     */
    public function user_products_delete(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules,$custom_errors = []);

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product_details = \App\UserProduct::destroy($request->user_product_id);

            DB::commit();

            $data['user_product_id'] = $request->user_product_id;

            return $this->sendResponse(api_success(123), $success_code = 123, $data);
            
        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }       
         
    }

    /**
     * @method user_products_update_availability
     *
     * @uses To update stardom product - Available / Out of Stock
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function user_products_update_availability(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product_details->is_outofstock = $user_product_details->is_outofstock ? PRODUCT_NOT_AVAILABLE : PRODUCT_AVAILABLE;

            if($user_product_details->save()) {

                DB::commit();

                $success_code = $user_product_details->is_outofstock ? 126 : 127;

                $data['user_product_details'] = $user_product_details;

                return $this->sendResponse(api_success($success_code),$success_code, $data);

            }
            
            throw new Exception(api_error(130), 130);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_products_set_visibility
     *
     * @uses To update stardom product status as DECLINED/APPROVED
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function user_products_set_visibility(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product_details->is_visible = $user_product_details->is_visible ? NO : YES;

            if($user_product_details->save()) {

                DB::commit();

                $success_code = $user_product_details->is_visible ? 124 : 125;

                $data['user_product_details'] = $user_product_details;

                return $this->sendResponse(api_success($success_code),$success_code, $data);

            }
            
            throw new Exception(api_error(130), 130);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method product_categories
     *
     * @uses List Product Categories
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function product_categories(Request $request) {

        try {

            $product_categories = \App\Category::where('status',APPROVED)->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $product_categories = selected($product_categories, '', 'id');

            if($request->user_product_id) {

                $user_product_details = \App\UserProduct::find($request->user_product_id);

                $category_id = $user_product_details->category_id;

                $product_categories = selected($product_categories, $category_id, 'id');
            }

            $data['product_categories'] = $product_categories;

            return $this->sendResponse($message = "", $success_code = "", $data);
            

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method product_sub_categories
     *
     * @uses List Product Sub Categories
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function product_sub_categories(Request $request) {

        try {

            $product_sub_categories = \App\SubCategory::where('status',APPROVED)->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $product_sub_categories = selected($product_sub_categories, '', 'id');

            if($request->user_product_id) {

                $user_product_details = \App\UserProduct::find($request->user_product_id);
                
                $sub_category_id = $user_product_details->sub_category_id;

                $product_sub_categories = selected($product_sub_categories, $sub_category_id, 'id');

            }

            $data['product_sub_categories'] = $product_sub_categories;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

}