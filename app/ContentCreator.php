<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Setting, DB;

use App\Helpers\Helper;

class ContentCreator extends Model
{
    public function stardomDocuments() {
   
        return $this->hasMany(StardomDocument::class, 'user_id');
   
    }

    public function stardomProducts() {

    	return $this->hasMany(UserProduct::class,'user_id');
    }

    public function posts() {

        return $this->hasMany(Post::class, 'user_id');
    }

    public function postAblums() {

        return $this->hasMany(PostAlbum::class, 'user_id');
    }


    public static function boot() {

        parent::boot();

        static::deleting(function ($model) {

            Helper::delete_file($model->picture , STARDOM_FILE_PATH);

            $model->stardomDocuments()->delete();

            $model->stardomProducts()->delete();

            $model->posts()->delete();

            $model->postAblums()->delete();

        });

    }
}
