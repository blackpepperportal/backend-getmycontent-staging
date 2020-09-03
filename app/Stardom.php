<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stardom extends Model
{
    public function stardomDocuments() {
   
        return $this->hasMany(StardomDocument::class, 'stardom_id');
   
    }

    public function stardomProducts() {

    	return $this->hasMany(stardomProduct::class,'stardom_id');
    }

    public function stardomProductPictures() {

    	return $this->hasMany(StardomProductPicture::class,'stardom_id');
    }

    public function stardomWallets() {

    	return $this->hasMany(StardomWallet::class,'stardom_id');
    }

    public function stardomWalletPayments() {

    	return $this->hasMany(StardomWalletPayment::class,'stardom_id');
    }

    public function stardomWithDrawals() {

    	return $this->hasMany(StardomWithDrawal::class,'stardom_id');
    }

    public static function boot() {

        parent::boot();

        static::deleting(function ($model) {

            Helper::delete_file($model->picture , STARDOM_FILE_PATH);

            $model->stardomDocuments()->delete();

            $model->stardomProducts()->delete();

            $model->stardomProductPictures()->delete();

            $model->stardomWallets()->delete();

            $model->stardomWalletPayments()->delete();

            $model->stardomWithDrawals()->delete();

        });

    }
}
