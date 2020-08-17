<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	protected $appends = ['post_amount_formatted'];

	public function getPostAmountFormattedAttribute() {

		return formatted_amount($this->amount);
	}

    public function getStardomDetails() {

    	return $this->belongsTo(Stardom::class,'stardom_id');
    }
}
