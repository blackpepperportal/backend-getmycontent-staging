<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\User, App\UserProductPicture;

use App\Repositories\ProductRepository;

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

            $user_product = \App\UserProduct::find($request->user_product_id) ?? new \App\UserProduct;

            $success_code = $user_product->id ? 122 : 121;

            $user_product->user_id = $request->id;

            $user_product->name = $request->name ?: $user_product->name;

            $user_product->quantity = $request->quantity ?: $user_product->quantity;

            $user_product->price = $request->price ?: '';

            $user_product->category_id = $request->category_id ?: $user_product->category_id;

            $user_product->sub_category_id = $request->sub_category_id ?: $user_product->sub_category_id;

            $user_product->description = $request->description ?: '';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_product_id) {

                    Helper::storage_delete_file($user_product->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $user_product->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($user_product->save()) {

                DB::commit(); 

                $data = \App\UserProduct::find($user_product->id);

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

            $user_product = \App\UserProduct::find($request->user_product_id);

            if(!$user_product) { 

                throw new Exception(api_error(133), 133);                
            }

            $data['user_product'] = $user_product;

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

            $user_product = \App\UserProduct::find($request->user_product_id);

            if(!$user_product) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product = \App\UserProduct::destroy($request->user_product_id);

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

            $user_product = \App\UserProduct::find($request->user_product_id);

            if(!$user_product) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product->is_outofstock = $user_product->is_outofstock ? PRODUCT_NOT_AVAILABLE : PRODUCT_AVAILABLE;

            if($user_product->save()) {

                DB::commit();

                $success_code = $user_product->is_outofstock ? 126 : 127;

                $data['user_product'] = $user_product;

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

            $user_product = \App\UserProduct::find($request->user_product_id);

            if(!$user_product) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product->is_visible = $user_product->is_visible ? NO : YES;

            if($user_product->save()) {

                DB::commit();

                $success_code = $user_product->is_visible ? 124 : 125;

                $data['user_product'] = $user_product;

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

                $user_product = \App\UserProduct::find($request->user_product_id);

                $category_id = $user_product->category_id;

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

                $user_product = \App\UserProduct::find($request->user_product_id);
                
                $sub_category_id = $user_product->sub_category_id;

                $product_sub_categories = selected($product_sub_categories, $sub_category_id, 'id');

            }

            $data['product_sub_categories'] = $product_sub_categories;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_products_search
     *
     * @uses Search Products
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - search_key
     * 
     * @return response success/failure message
     *
     **/
    public function user_products_search(Request $request) {

        try {

            $rules = [
                'search_key' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);

            $base_query = \App\UserProduct::orderBy('updated_at','desc');

            $search_key = $request->search_key;

            if($search_key) {

                $base_query = $base_query 

                    ->where(function ($query) use ($search_key) {

                        return $query->Where('user_products.name','LIKE','%'.$search_key.'%');

                    })->orWhereHas('productCategories', function($q) use ($search_key) {

                        return $q->Where('categories.name','LIKE','%'.$search_key.'%');

                    })->orWhereHas('productSubCategories', function($q) use ($search_key) {

                        return $q->Where('sub_categories.name','LIKE','%'.$search_key.'%');

                    });
            }

            $user_products = $base_query->skip($this->skip)->take($this->take)->get();

            $data['user_products'] = $user_products;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_product_pictures()
     *
     * @uses To load product images
     *
     * @created Bhawya N
     *
     * @updated 
     *
     * @param model image object - $request
     *
     * @return response of succes failure 
     */
    public function user_product_pictures(Request $request) {

        try {

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_pictures = UserProductPicture::where('user_product_id', $request->user_product_id)
                ->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();
               
            $data['user_product_pictures'] = $user_product_pictures;

            return $this->sendResponse($message = "", $success_code = "", $data);
                
        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method user_product_pictures_save()
     *
     * @uses To save gallery product pictures
     *
     * @created Bhawya N
     *
     * @updated 
     *
     * @param object $request - Model Object
     *
     * @return response of success / Failure
     */
    public function user_product_pictures_save(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id,
                'picture.*' => 'required|picture|mimes:jpg,jpeg,png',
            ];

            Helper::custom_validator($request->all(),$rules);
            
            if($request->hasfile('picture')) {

                ProductRepository::user_product_pictures_save($request->file('picture'), $request->user_product_id);

                DB::commit();

                return $this->sendResponse(api_success(133), $success_code = 133, $data = '');
            
            }

            throw new Exception(api_error(130), 130);
            
        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_product_pictures_delete()
     *
     * @uses To delete Product Image
     *
     * @created  Bhawya N
     *
     * @updated  
     *
     * @param model image object - $request
     *
     * @return response of succes failure 
     */
    public function user_product_pictures_delete(Request $request) {

        try {

            $rules = [
                'user_product_picture_id' => 'required|exists:user_product_pictures,id'
            ];

            Helper::custom_validator($request->all(),$rules);
                
            $user_product_pictures = \App\UserProductPicture::find($request->user_product_picture_id);

            if(!$user_product_pictures) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product_pictures = \App\UserProductPicture::destroy($request->user_product_picture_id);

            DB::commit();

            $data['user_product_picture_id'] = $request->user_product_picture_id;

            return $this->sendResponse(api_success(132), $success_code = 132, $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

}