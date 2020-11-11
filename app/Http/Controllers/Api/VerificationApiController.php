<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\User;

class VerificationApiController extends Controller
{
    protected $loginUser, $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::CommonResponse()->find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /** 
     * @method documents_list()
     *
     * @uses To display the user details based on user  id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function documents_list(Request $request) {

        try {

        	$documents = \App\Document::CommonResponse()->get();

            $is_delete_edit_option = $this->loginUser->is_document_verified == USER_DOCUMENT_APPROVED ? NO : YES;

        	foreach ($documents as $key => $document) {

        		$is_user_uploaded = NO;

        		// Check the user uploaded the document

        		$user_document = \App\UserDocument::where('user_id', $request->id)->where('document_id', $document->document_id)->CommonResponse()->first();

        		$document->is_user_uploaded = $user_document ? YES : NO;

        		$document->user_document = $user_document ?? [];

                $document->is_delete_edit_option = $is_delete_edit_option;
        	}

        	$data['documents'] = $documents;

            $data['document_status_formatted'] = document_status_formatted($this->loginUser->is_document_verified ?? 0);

            $data['is_document_verified'] = $this->loginUser->is_document_verified ?? 0;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method documents_save()
     *
     * @uses To display the user details based on user  id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function documents_save(Request $request) {

        try {

        	DB::beginTransaction();

             // Validation start

            $rules = [
            		'document_id' => 'required|exists:documents,id',
                    'document_file' => 'required',
            	];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $request->request->add(['user_id' => $request->id]);

        	$document = \App\UserDocument::updateOrCreate(['document_id' => $request->document_id, 'user_id' => $request->id], $request->all());

        	$document->document_file = \Helper::storage_upload_file($request->file('document_file'), DOCUMENTS_PATH);

            $document->document_file_front = \Helper::storage_upload_file($request->file('document_file_front'), DOCUMENTS_PATH);
            
            $document->document_file_back = \Helper::storage_upload_file($request->file('document_file_back'), DOCUMENTS_PATH);

        	$document->save();

            if($user = User::find($request->id)) {

                if($user->is_document_verified != USER_DOCUMENT_APPROVED) {

                    $user->is_document_verified = USER_DOCUMENT_PENDING;

                    $user->save();
                }
            }

        	DB::commit();

            return $this->sendResponse(api_success(114), $success_code = 114, $document);

        } catch(Exception $e) {

        	DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method documents_delete()
     *
     * @uses To display the user details based on user  id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function documents_delete(Request $request) {

        try {

            DB::beginTransaction();

             // Validation start

            $rules = ['user_document_id' => 'required|exists:user_documents,id,user_id,'.$request->id];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

        	$user_billing_account = \App\UserDocument::destroy($request->user_document_id);

        	DB::commit();

        	$data['user_document_id'] = $request->user_document_id;

            return $this->sendResponse(api_success(115), $success_code = 115, $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method documents_delete_all()
     *
     * @uses delete user uploaded all documents
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function documents_delete_all(Request $request) {

        try {

            DB::beginTransaction();

        	\App\UserDocument::where('user_id', $request->id)->delete();

        	DB::commit();

            return $this->sendResponse(api_success(116), $success_code = 116, $data = []);

        } catch(Exception $e) {
        	
        	DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_documents_status()
     *
     * @uses To display the user details based on user  id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_documents_status(Request $request) {

        try {

            $user = User::where('id', $request->id)->select('users.id', 'is_document_verified')->first();

            if(!$user) { 

                throw new Exception(api_error(1002) , 1002);
            }

            return $this->sendResponse($message = "", $success_code = "", $user);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

}
