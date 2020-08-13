<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StardomDocument extends Model
{
    public function stardomDetails() {

    	return $this->belongsTo(Stardom::class,'stardom_id');
    }

    public function DocumentDetails() {

    	return $this->belongsTo(Document::class,'document_id');
    }
}
