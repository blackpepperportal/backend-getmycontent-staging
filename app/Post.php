<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function getStardomDetails() {

    	return $this->belongsTo(Stardom::class,'stardom_id');
    }
}
