<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProductPicture extends Model
{
    protected $appends = ['user_product_picture_id'];

    protected $hidden = ['id'];

	public function getUserProductPictureIdAttribute() {

		return $this->id;
	}
}
