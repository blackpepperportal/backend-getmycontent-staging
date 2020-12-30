<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCategory extends Model
{
    //

    public function ucategory(){

        return $this->belongsTo(UCategory::class,'u_category_id');
    }

    public function user(){

        return $this->belongsTo(User::class,'user_id');
    }
}
