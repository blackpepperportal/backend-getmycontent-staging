<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	protected $hidden = ['id','unique_id'];

	protected $appends = ['amount_formatted','post_id','post_unique_id', 'username', 'user_picture', 'user_unique_id'];

	public function getAmountFormattedAttribute() {

		return formatted_amount($this->amount);
	}

	public function getPostIdAttribute() {

		return $this->id;
	}

	public function getPostUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getUserUniqueIdAttribute() {

		return $this->user->unique_id ?? "";
	}

	public function getUsernameAttribute() {

		return $this->user->name ?? "";
	}

	public function getUserPictureAttribute() {

		return $this->user->picture ?? "";
	}

	public function user() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public function postFiles() {

	   return $this->hasMany(PostFile::class,'post_id');
	}

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaidApproved($query) {

        $query->where('posts.is_paid_post', YES)->where('posts.amount', '>', 0);

        return $query;

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
