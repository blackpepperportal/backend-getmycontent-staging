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

    public function user() {

    	return $this->belongsTo(User::class,'user_id');
    } 

    public function deliveryAddressDetails() {

    	return $this->belongsTo(DeliveryAddress::class,'delivery_address_id');
    }

    public function orderProducts() {

        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function orderPayments() {

        return $this->hasMany(OrderPayment::class, 'order_id');
    }

    public static function boot() {

        parent::boot();

        static::deleting(function ($model) {

            $model->orderProducts()->delete();

            $model->orderPayments()->delete();

        });

    }
}
