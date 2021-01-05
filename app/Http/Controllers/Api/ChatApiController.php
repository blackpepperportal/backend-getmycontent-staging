<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Log, Validator, Exception, DB, Setting;

use App\Helpers\Helper;

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
                'amount' => $request->is_file_uploaded ? 'required' : '',
                'file' => $request->is_file_uploaded ? 'required' : '',
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

            $chat_message->is_file_uploaded = $request->is_file_uploaded ?? YES;

            $chat_message->amount = $request->amount ?? 0.00;

            if ($chat_message->save()) {

                if ($request->is_file_uploaded) {

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
                }

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


}
