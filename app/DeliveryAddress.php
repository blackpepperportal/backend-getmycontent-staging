<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
	public function user() {

		return $this->belongsTo(User::class, 'user_id');
	}    
}
