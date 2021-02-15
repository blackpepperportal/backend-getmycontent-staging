<?php

namespace App\Http\Controllers\CategoryCustom\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use DB, Log, Hash, Validator, Exception, Setting, Helper;

use App\User;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\WalletRepository as WalletRepo;


class UCategoryApiController extends Controller
{
    protected $loginUser, $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

        $request->request->add(['timezone' => $this->timezone]);

    }    

    /** 
     * @method u_categories_list()
     *
     * @uses ucategories List
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function u_categories_list(Request $request) {

        try {

            $base_query = $total_query = \App\UCategory::CommonResponse()->Approved();

            $u_categories = $base_query->orderBy('u_categories.created_at', 'desc')->get();

            $data['u_categories'] = $u_categories;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $u_categories);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method u_categories_view()
     *
     * @uses ucategories single view
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     *
     * @return json response with details
     */

    public function u_categories_view(Request $request) {

        try {

            $rules = ['u_category_unique_id' => 'required|exists:u_categories,unique_id'];

            $custom_errors = ['u_category_unique_id.exists' => api_error(300)];

            Helper::custom_validator($request->all(),$rules,$custom_errors);

            $u_category = \App\UCategory::where('unique_id', $request->u_category_unique_id)->CommonResponse()->first();

            $data['u_category'] = $u_category;

            $base_query = $u_category->userCategories()->whereHas('user')->get();

            foreach($base_query as $user) {

                $user->followers_count = $user->user->followers->where('status',FOLLOWER_ACTIVE)->count() ?? 0;

            };

            $data['users'] = $base_query->sortByDesc('followers_count')->values() ?? [];

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
}
