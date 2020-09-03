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

    public function stardomDetails(){

    	return $this->belongsTo(Stardom::class,'stardom_id');
    }

    public function stardomProductPictures() {

        return $this->hasMany(StardomProductPicture::class,'stardom_product_id');
    }

    public function orderProducts() {

        return $this->hasMany(orderProduct::class,'stardom_product_id');
    }

    public static function boot() {

        parent::boot();

        static::deleting(function ($model) {

            Helper::delete_file($model->picture , STARDOM_FILE_PATH);

            $model->stardomProductPictures()->delete();

            $model->orderProducts()->delete();

        });

    }
}
