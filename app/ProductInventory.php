<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    
	protected $appends = ['total_quantity_formatted','used_quantity_formatted','remaining_quantity_formatted'];

	public function getTotalQuantityFormattedAttribute() {

		return formatted_amount($this->total_quantity);
	}

	public function getUsedQuantityFormattedAttribute() {

		return formatted_amount($this->used_quantity);
	}

	public function getRemainingQuantityFormattedAttribute() {

		return formatted_amount($this->remaining_quantity);
	}

    public function stardomProductDetails() {

    	return $this->belongsTo(StardomProduct::class,'stardom_product_id');
    }
}
