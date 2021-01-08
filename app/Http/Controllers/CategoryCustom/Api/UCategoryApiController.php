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
     * @method user_wallets_index()
     * 
     * @uses wallet details
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_index(Request $request) {

        try {

            $user_wallet = \App\UserWallet::firstWhere('user_id', $request->id);
            
            if(!$user_wallet) {

                $user_wallet = \App\UserWallet::create(['user_id' => $request->id, 'total' => 0.00, 'used' => 0.00, 'remaining' => 0.00]);

            }

            $data['user_wallet'] = $user_wallet;

            $data['user_withdrawals_min_amount'] = Setting::get('user_withdrawals_min_amount', 10);

            $data['user_withdrawals_min_amount_formatted'] = formatted_amount(Setting::get('user_withdrawals_min_amount', 10));

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

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

            $base_query = $total_query = \App\UCategory::CommonResponse();

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

            $rules = ['u_category_id' => 'required|exists:u_categories,id'];

            $custom_errors = ['u_category_id.exists' => api_error(300)];

            Helper::custom_validator($request->all(),$rules,$custom_errors);

            $u_category = \App\UCategory::where('id', $request->u_category_id)->CommonResponse()->get();

            $data['u_category'] = $u_category;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
}
