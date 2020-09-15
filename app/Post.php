<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	protected $appends = ['amount_formatted','post_id'];

	protected $hidden = ['id'];

	public function getAmountFormattedAttribute() {

		return formatted_amount($this->amount);
	}

	public function getPostIdAttribute() {

		return $this->id;
	}

    public function getuserDetails() {

    	return $this->belongsTo(ContentCreator::class,'user_id');
    }
}
