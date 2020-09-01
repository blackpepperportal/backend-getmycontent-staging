<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{

    public function stardomProductDetails() {

    	return $this->belongsTo(StardomProduct::class,'stardom_product_id');
    }
}
