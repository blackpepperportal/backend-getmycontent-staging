<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\User;

class PostRepository {

    /**
     * @method post_fil()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function post_fil($payments, $request) {

        $payments = $payments->map(function ($value, $key) use ($request) {

                        if($value->payment_type == WALLET_PAYMENT_TYPE_CREDIT) {

                            if($request->id == $value->user_id) {

                                $value->username = $value->ReceivedFromUser->name ?? "-";

                                unset($value->ReceivedFromUser);

                            }
                        } else {
                            $value->username = $value->toUser->name ?? "You";

                            unset($value->toUser);
                        }

                        $value->dispute_btn_status = in_array($value->status, [USER_WALLET_PAYMENT_PAID]) ? YES : NO;

                        $value->paid_date = common_date($value->paid_date, $request->timezone, 'd M Y');

                        return $value;
                    });


        return $payments;

    }
}