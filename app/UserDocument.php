<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    public function userDetails() {

    	return $this->belongsTo(User::class, 'user_id');
    }

    public function DocumentDetails() {

    	return $this->belongsTo(Document::class, 'document_id');
    }
}
