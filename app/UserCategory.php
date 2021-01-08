<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCategory extends Model
{

	protected $hidden = ['id', 'unique_id'];

	protected $appends = ['user_category_id','user_category_unique_id', 'username'];

    public function getUserCategoryIdAttribute() {

        return $this->id;
    }

    public function getUserCategoryUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getUserNameAttribute() {

        return $this->user->name ?? "";
    }

    public function ucategory(){

        return $this->belongsTo(UCategory::class,'u_category_id');
    }

    public function user(){

        return $this->belongsTo(User::class,'user_id');
    }
}
