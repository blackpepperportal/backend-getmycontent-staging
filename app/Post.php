<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	protected $hidden = ['id','unique_id'];

	protected $appends = ['amount_formatted','post_id','post_unique_id'];

	public function getAmountFormattedAttribute() {

		return formatted_amount($this->amount);
	}

	public function getPostIdAttribute() {

		return $this->id;
	}

	public function getPostUniqueIdAttribute() {

		return $this->unique_id;
	}

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PF"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PF"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
