<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StardomWithDrawal extends Model
{
	protected $appends = ['requested_amount_formatted','paid_amount_formatted'];

	public function getRequestedAmountFormattedAttribute() {

		return formatted_amount($this->requested_amount);
	}

	public function getPaidAmountFormattedAttribute() {

		return formatted_amount($this->paid_amount);
	}

    public function stardomDetails() {

    	return $this->belongsTo(Stardom::class,'stardom_id');
    }
}
