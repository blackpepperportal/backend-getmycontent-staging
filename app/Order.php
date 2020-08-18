<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $appends = ['total_formatted','tax_price_formatted','sub_total_formatted'];

    public function getTotalFormattedAttribute() {

    	return formatted_amount($this->total);
    }

    public function getTaxPriceFormattedAttribute() {

    	return formatted_amount($this->tax_price);
    }

    public function getSubTotalFormattedAttribute() {

    	return formatted_amount($this->sub_total);
    }

    public function userDetails() {

    	return $this->belongsTo(User::class,'user_id');
    } 

    public function delivaryAddressDetails() {

    	return $this->belongsTo(DeliveryAddress::class,'delivary_address_id');
    }
}
