<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'comment'];

    protected $hidden = ['id','unique_id'];

	protected $appends = ['post_comment_id','post_comment_unique_id', 'username', 'user_picture'];
	
	public function getPostCommentIdAttribute() {

		return $this->id;
	}

	public function getPostCommentUniqueIdAttribute() {

		return $this->unique_id;
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

	public function post() {

	   return $this->belongsTo(Post::class, 'post_id');
	}

		/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('post_comments.status', APPROVED);

        return $query;

    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PC"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PC"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
