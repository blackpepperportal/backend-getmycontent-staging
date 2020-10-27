<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

class CommonRepository {

	/**
     *
     * @method user_premium_account_check()
     *
     * @uses To Upoad Product Pictures
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param 
     *
     * @return
     */
    public static function user_premium_account_check($user) {

        try {

            if($user->is_email_verified == USER_EMAIL_NOT_VERIFIED) {

                throw new Exception(api_error(157), 157);
                
            }

            if($user->is_document_verified != USER_DOCUMENT_APPROVED) {

                $code = $user->userDocuments->count() ? 158 : 160;

                if($user->is_document_verified == USER_DOCUMENT_DECLINED) {

                    $code = 159;

                }

                throw new Exception(api_error($code), $code);
                
            }

            $check_billing_accounts = $user->userBillingAccounts->where('user_billing_accounts.is_default', YES)->first();

            if($check_billing_accounts) {

                throw new Exception(api_error(161), 161);
            }

            $response = ['success' => true, 'message' => api_success('')];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }
}