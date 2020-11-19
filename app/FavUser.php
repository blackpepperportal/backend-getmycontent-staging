<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavUser extends Model
{
    protected $fillable = ['user_id', 'fav_user_id'];

    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['fav_user_id', 'fav_user_unique_id', 'username', 'user_picture', 'fav_username', 'fav_user_picture'];
	
	public function getFavUserIdAttribute() {

		return $this->id;
	}

	public function getFavUserUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getUsernameAttribute() {

		return $this->user->name ?? "";
	}

	public function getUserPictureAttribute() {

		return $this->user->picture ?? "";
	}

	public function getFavUsernameAttribute() {

		return $this->favUser->name ?? "";
	}

	public function getFavUserPictureAttribute() {

		return $this->favUser->picture ?? "";
	}

	public function favUser() {

	   return $this->belongsTo(User::class, 'fav_user_id');
	}

	public function user() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('fav_users.status', APPROVED);

        return $query;

    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "FP-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "FP-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
