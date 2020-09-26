<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    public function userDetails() {

    	return $this->belongsTo(User::class,'user_id');
    }
}