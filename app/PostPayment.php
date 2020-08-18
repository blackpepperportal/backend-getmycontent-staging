<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostPayment extends Model
{
	protected  $appends = ['paid_amount_formatted'];

    public function getPaidAmountFormattedAttribute() {

    	return formatted_amount($this->paid_amount);
    }

    public function userDetails() {

    	return $this->belongsTo(User::class,'user_id');
    }
}
