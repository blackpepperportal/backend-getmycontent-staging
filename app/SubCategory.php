<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];


    protected $appends = ['sub_category_id'];

    public function getSubCategoryIdAttribute() {

        return $this->id;
    }
}
