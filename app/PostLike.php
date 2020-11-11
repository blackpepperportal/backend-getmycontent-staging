<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $fillable = ['user_id', 'post_id', 'post_user_id'];

    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['post_like_id', 'post_like_unique_id', 'username', 'user_picture'];
	
	public function getPostLikeIdAttribute() {

		return $this->id;
	}

	public function getPostLikeUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getUsernameAttribute() {

		$name = $this->postUser->name ?? "";

		unset($this->postUser);

		return $name;
	}

	public function getUserPictureAttribute() {

		$picture = $this->postUser->picture ?? "";

		unset($this->postUser);

		return $picture ?? "";
	}

	public function postUser() {

	   return $this->belongsTo(User::class, 'post_user_id');
	}

	public function User() {

		return $this->belongsTo(User::class, 'user_id');
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

        $query->where('post_likes.status', APPROVED);

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
