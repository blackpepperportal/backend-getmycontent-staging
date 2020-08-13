<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StardomProduct extends Model
{
    //

    protected $appends = ['stardom_product_id','stardom_product_price_formatted'];

    public function getStardomProductIdAttribute() {

        return $this->id;
    }


    public function getStardomProductPriceFormattedAttribute() {

        return formatted_amount($this->price);
    }
}
