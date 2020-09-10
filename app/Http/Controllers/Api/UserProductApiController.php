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

	        $user_products = \App\UserProduct::where('user_id',$request->id)
	        	->orderBy('created_at','DESC')
	        	->skip($this->skip)
	        	->take($this->take)
	        	->get();

       		return $this->sendResponse($message = "", $code = "", $user_products);

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
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = \App\UserProduct::find($request->user_product_id) ?? new \App\UserProduct;

            $success_code = $user_product_details->id ? 122 : 121;

            $user_product_details->user_id = $request->id;

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
                'user_product_id' => 'required|exists:user_products,id'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(api_error(133), 133);                
            }

            return $this->sendResponse($message = "", $success_code = "", $user_product_details);
            
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
                'user_product_id' => 'required|exists:user_products,id'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(api_error(133), 133);                
            }

            if($user_product_details->delete()) {

                DB::commit();

                $response_array = ['success' => true , 'message' => api_sucess(123)];

                return response()->json($response_array , 200);

            } 
            
            throw new Exception(api_error(134), 134);
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

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
                'user_product_id' => 'required|exists:user_products,id'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product_details->is_outofstock = $user_product_details->is_outofstock ? PRODUCT_AVAILABLE : PRODUCT_NOT_AVAILABLE;

            if($user_product_details->save()) {

                DB::commit();

                $message = $user_product_details->is_outofstock ? api_success(126) : api_success(127);

            	return $this->sendResponse($message, 200);

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
                'user_product_id' => 'required|exists:user_products,id'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_details = \App\UserProduct::find($request->user_product_id);

            if(!$user_product_details) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product_details->is_visible = $user_product_details->is_visible ? NO : YES;

            if($user_product_details->save()) {

                DB::commit();

                $message = $user_product_details->status ? api_success(124) : api_success(125);

            	return $this->sendResponse($message, 200);

            }
            
            throw new Exception(api_error(130), 130);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

}