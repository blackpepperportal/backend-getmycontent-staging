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
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function u_categories_list(Request $request) {

        try {

            $base_query = $total_query = \App\UCategory::CommonResponse();

            $u_categories = $base_query->skip($this->skip)->take($this->take)->orderBy('u_categories.created_at', 'desc')->get();

            $data['u_categories'] = $u_categories;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $u_categories);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }
}
