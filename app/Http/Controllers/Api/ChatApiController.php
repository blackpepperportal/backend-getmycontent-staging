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
                'message' => 'required',
                'amount' => 'required',
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
     * @updated Arun
     * 
     * @param 
     *
     * @return return response.
     *
     */

    public function chat_assets_index(Request $request) {

        try {
            
            $sent_base_query = \App\ChatAsset::where('is_paid',PAID)->where('from_user_id',$request->id);

            $sent = $sent_base_query->skip($this->skip)->take($this->take)->get();

            $recived_base_query = \App\ChatAsset::where('is_paid',PAID)->where('to_user_id',$request->id);

            $received = $recived_base_query->skip($this->skip)->take($this->take)->get();

            $data['sent'] = $sent ?? [];

            $data['received'] = $received ?? [];

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

                throw new Exception(api_error(167), 167); 

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

                return $this->sendResponse(api_success(117), 117, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


}
