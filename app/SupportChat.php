<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupportChat extends Model
{
    public function user() {

    	return $this->belongsTo(User::class,'user_id');
    }
}
