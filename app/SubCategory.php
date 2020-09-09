<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    public function categoryDetails() {

    	return $this->belongsTo(Category::class,'category_id');
    }
}
