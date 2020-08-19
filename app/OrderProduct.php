<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $appends = ['delivery_price_formatted','sub_total_formatted','tax_formatted','sub_total_formatted','per_quantity_price_formatted'];


    public function getTotalFormattedAttribute() {

    	return formatted_amount($this->total);
    }

    public function getTaxPriceFormattedAttribute() {

    	return formatted_amount($this->tax_price);
    }

    public function getSubTotalFormattedAttribute() {

    	return formatted_amount($this->sub_total);
    }

    public function getDeliveryPriceFormattedAttribute() {

    	return formatted_amount($this->delivery_price);
    }

    public function getPerQuantityPriceFormattedAttribute() {

        return formatted_amount($this->per_quantity_price);
    }

    public function stardomProductDetails() {

    	return $this->belongsTo(StardomProduct::class,'stardom_product_id');
    }
}
