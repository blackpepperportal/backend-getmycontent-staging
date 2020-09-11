<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
    protected $appends = ['user_product_id','user_product_price_formatted'];

    public function getUserProductIdAttribute() {

        return $this->id;
    }

    public function getUserProductPriceFormattedAttribute() {

        return formatted_amount($this->price);
    }

    public function userDetails(){

    	return $this->belongsTo(User::class,'user_id');
    }

    public function userProductPictures() {

        return $this->hasMany(UserProductPicture::class,'user_product_id');
    }

    public function orderProducts() {

        return $this->hasMany(OrderProduct::class,'user_product_id');
    }

    public function productCategories() {

        return $this->belongsTo(Category::class,'category_id');
    }

    public function productSubCategories() {

        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

    public static function boot() {

        parent::boot();

        static::deleting(function ($model) {

            Helper::delete_file($model->picture, PRODUCT_FILE_PATH);

            $model->userProductPictures()->delete();

            $model->orderProducts()->delete();

        });

    }
}
