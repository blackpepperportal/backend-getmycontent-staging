<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];

    protected $appends = ['category_id'];

    public function getCategoryIdAttribute() {

        return $this->id;
    }

}
