<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostAlbum extends Model
{
    public function stardomDetails() {

    	return $this->belongsTo(Stardom::class,'stardom_id');
    }
}
