<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	protected $appends = ['amount_formatted'];

	public function getAmountFormattedAttribute() {

		return formatted_amount($this->amount);
	}

    public function getStardomDetails() {

    	return $this->belongsTo(Stardom::class,'stardom_id');
    }
}
