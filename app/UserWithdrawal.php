<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWithdrawal extends Model
{
    protected $appends = ['requested_amount_formatted','paid_amount_formatted'];

	public function getRequestedAmountFormattedAttribute() {

		return formatted_amount($this->requested_amount);
	}

	public function getPaidAmountFormattedAttribute() {

		return formatted_amount($this->paid_amount);
	}

    public function userDetails() {

    	return $this->belongsTo(User::class,'user_id');
	}
	
	public function billingaccountDetails() {

		return $this->belongsTo('App\UserBillingAccount','user_billing_account_id');
		
    }
}
