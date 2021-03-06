<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Log, Validator, Exception, DB, Setting;

use App\Helpers\Helper;

use App\Repositories\PaymentRepository as PaymentRepo;

class ChatApiController extends Controller
{

    protected $skip, $take;

    public function __construct(Request $request) {

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method chat_assets_save()
     * 
     * @uses - To save the chat assets.
     *
     * @created Arun
     *
     * @updated Arun
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function chat_assets_save(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|exists:users,id',
                'message' => '',
                'amount' => 'nullable|min:0',
                'file' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);
            
            $message = $request->message;

            $from_chat_user_inputs = ['from_user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id];

            $from_chat_user = \App\ChatUser::updateOrCreate($from_chat_user_inputs);

            $to_chat_user_inputs = ['from_user_id' => $request->to_user_id, 'to_user_id' => $request->from_user_id];

            $to_chat_user = \App\ChatUser::updateOrCreate($to_chat_user_inputs);

            $chat_message = new \App\ChatMessage;

            $chat_message->from_user_id = $request->from_user_id;

            $chat_message->to_user_id = $request->to_user_id;

            $chat_message->message = $request->message;

            $chat_message->is_file_uploaded = YES;

            $chat_message->amount = $request->amount ?? 0.00;

            if ($chat_message->save()) {

                $chat_asset = new \App\ChatAsset;

                $chat_asset->from_user_id = $request->from_user_id;

                $chat_asset->to_user_id = $request->to_user_id;

                $chat_asset->chat_message_id = $chat_message->chat_message_id;

                $filename = rand(1,1000000).'-chat_asset-'.$request->file_type;

                $chat_assets_file_url = Helper::storage_upload_file($request->file, CHAT_ASSETS_PATH, $filename);

                $chat_asset->file = $chat_assets_file_url;

                $chat_asset->file_type = $request->file_type ?? FILE_TYPE_IMAGE;

                $chat_asset->amount = $request->amount ?? 0.00;

                $chat_asset->save();

                DB::commit();
            }

            $data['chat_message'] = $chat_message;

            $data['chat_asset'] = $chat_asset;

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }
    
    /**
     * @method chat_assets_index()
     * 
     * @uses - To get the media assets.
     *
     * @created Arun
     *
     * @updated Vithya R
     * 
     * @param 
     *
     * @return return response.
     *
     */

    public function chat_assets_index(Request $request) {

        try {

            $base_query = $total_query = \App\ChatAsset::where(function($query) use ($request){
                        $query->where('chat_assets.from_user_id', $request->from_user_id);
                        $query->where('chat_assets.to_user_id', $request->to_user_id);
                    })->orWhere(function($query) use ($request){
                        $query->where('chat_assets.from_user_id', $request->to_user_id);
                        $query->where('chat_assets.to_user_id', $request->from_user_id);
                    })
                    ->latest();
                    
            $chat_assets = $base_query->skip($this->skip)->take($this->take)->get();
            
            $data['chat_assets'] = $chat_assets ?? emptyObject();

            $data['total'] = $total_query->count() ?? [];

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method chat_assets_payment_by_stripe()
     * 
     * @uses chat_assets_payment_by_stripe based on Chat message id
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param object $request - Chat message id
     *
     * @return json with boolean output
     */

    public function chat_assets_payment_by_stripe(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = ['chat_message_id' => 'required|numeric'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            $chat_message = \App\ChatMessage::firstWhere('id',$request->chat_message_id);

            $chat_asset = \App\ChatAsset::firstWhere('chat_message_id',$request->chat_message_id);

            if(!$chat_message || !$chat_asset) {

                throw new Exception(api_error(3000), 3000); 

            }

            $request->request->add(['payment_mode' => CARD]);

            $total = $user_pay_amount = $chat_message->amount ?: 0.00;

            if($user_pay_amount > 0) {

                // Check the user have the cards

                $user_card = \App\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120); 

                }

                $request->request->add([
                    'total' => $total, 
                    'user_card_id' => $user_card->id,
                    'customer_id' => $user_card->customer_id,
                    'card_token' => $user_card->card_token,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);

                $card_payment_response = PaymentRepo::chat_assets_payment_by_stripe($request, $chat_message)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);
               

            }

            $payment_response = PaymentRepo::chat_assets_payment_save($request, $chat_message)->getData();

            if($payment_response->success) {

                $chat_message->is_paid = PAID;

                if ($chat_message->save()) {

                    $chat_asset->is_paid = PAID;

                    $chat_asset->save();
                }
                
                DB::commit();

                return $this->sendResponse(api_success(118), 118, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method chat_assets_delete()
     *
     * @uses delete the chat assets
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function chat_assets_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['chat_message_id' => 'required|exists:chat_messages,id'];

            Helper::custom_validator($request->all(),$rules);

            $chat_message = \App\ChatMessage::destroy($request->chat_message_id);

            DB::commit(); 

            $data = $chat_message;

            return $this->sendResponse(api_success(3000), 3000, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /** 
     * @method chat_assets_payments_list()
     *
     * @uses To display the chat_assets_payments list based on user  id
     *
     * @created Arun 
     *
     * @updated Arun
     *
     * @param object $request
     *
     * @return json response with user details
     */

    public function chat_assets_payments_list(Request $request) {

        try {

            $base_query = $total_query = \App\ChatAssetPayment::where('from_user_id',$request->id);

            $chat_assets_payments = $base_query->skip($this->skip)->take($this->take)->get();

            $data['chat_assets_payments'] = $chat_assets_payments;

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method chat_assets_payments_view()
     * 
     * @uses get the selected chat_assets_payments request
     *
     * @created Arun 
     *
     * @updated Arun
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function chat_assets_payments_view(Request $request) {

        try {

            $rules = ['chat_asset_payments_id' => 'required|exists:chat_asset_payments,id'];

            Helper::custom_validator($request->all(),$rules);

            $chat_asset_payment = \App\ChatAssetPayment::with('chatMessage')->with('chatAssets')->firstWhere('id',$request->chat_asset_payments_id);
            
            if(!$chat_asset_payment) {

                throw new Exception(api_error(167), 167);
                
            }

            $data['chat_asset_payment'] = $chat_asset_payment;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


    /** 
     * @method chat_users_search()
     *
     * @uses
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
    public function chat_users_search(Request $request) {

        try {

            // validation start

            $rules = ['search_key' => 'required'];
            
            $custom_errors = ['search_key.required' => 'Please enter the username'];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $search_key = $request->search_key;

            $base_query = $total_query = \App\ChatUser::where('from_user_id', $request->id)
                    ->whereHas('toUser',function($query) use($search_key) {
                        return $query->where('users.name','LIKE','%'.$search_key.'%');
                    })
                    ->orderBy('chat_users.updated_at', 'desc');

            $chat_users = $base_query->skip($this->skip)->take($this->take)->get();

            $data['users'] = $chat_users ?? [];

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method chat_messages_search()
     *
     * @uses
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function chat_messages_search(Request $request) {

        try {

            $rules = ['search_key' => 'required'];
            
            $custom_errors = ['search_key.required' => 'Please enter the message'];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $search_key = $request->search_key;

            $base_query = $total_query = \App\ChatMessage::where(function($query) use ($request){
                        $query->where('chat_messages.from_user_id', $request->from_user_id);
                        $query->where('chat_messages.to_user_id', $request->to_user_id);
                    })
                    ->where('chat_messages.message', 'like', "%".$search_key."%")
                    ->orderBy('chat_messages.updated_at', 'asc');

            $chat_messages = $base_query->skip($this->skip)->take($this->take)->get();

            $data['messages'] = $chat_messages ?? [];

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

}
