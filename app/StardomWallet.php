<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StardomWallet extends Model
{
	protected $appends = ['total_formatted','onhold_formatted','used_formatted','remaining_formatted'];

	public function getTotalFormattedAttribute() {

		return formatted_amount($this->total);
	}

	public function getOnholdFormattedAttribute() {

		return formatted_amount($this->onhold);
	}

	public function getUsedFormattedAttribute() {

		return formatted_amount($this->used);
	}

	public function getRemainingFormattedAttribute() {

		return formatted_amount($this->remaining);
	}

    public function stardomDetails() {

    	return $this->belongsTo(Stardom::class,'stardom_id');
    }
}
