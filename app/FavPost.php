<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavPost extends Model
{
    protected $fillable = ['post_id', 'user_id', 'post_user_id'];

    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['fav_posts_id', 'fav_posts_unique_id', 'username', 'user_picture'];
	
	public function getFavPostIdAttribute() {

		return $this->id;
	}

	public function getFavPostUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getUsernameAttribute() {

		return $this->postUser->name ?? "";
	}

	public function getUserPictureAttribute() {

		return $this->postUser->picture ?? "";
	}

	public function postUser() {

	   return $this->belongsTo(User::class, 'post_user_id');
	}

	public function post() {

	   return $this->belongsTo(Post::class, 'post_id');
	}

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('fav_posts.status', APPROVED);

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
