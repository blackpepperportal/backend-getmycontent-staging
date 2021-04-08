<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	protected $hidden = ['id','unique_id'];

	protected $appends = ['amount_formatted','post_id','post_unique_id', 'username', 'user_displayname','user_picture', 'user_unique_id', 'total_likes', 'total_comments','is_verified_badge'];

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

		$user_unique_id = $this->user->unique_id ?? "";

		// unset($this->user);

		return $user_unique_id ?? "";
	}

	public function getUsernameAttribute() {

		$username = $this->user->username ?? "";

		// unset($this->user);

		return $username ?? "";
	}

	public function getUserDisplaynameAttribute() {

		$name = $this->user->name ?? "";

		// unset($this->user);

		return $name ?? "";
	}

	public function getIsVerifiedBadgeAttribute() {

		$is_verified_badge = $this->user->is_verified_badge ?? "";

		// unset($this->user);

		return $is_verified_badge ?? "";
	}

	public function getUserPictureAttribute() {

		$picture = $this->user->picture ?? "";

		// unset($this->user);

		return $picture ?? "";
	}

	public function getTotalLikesAttribute() {
		
	    return $this->hasMany(PostLike::class, 'post_id')->count();

	}

	public function getTotalCommentsAttribute() {
		
	    return $this->postComments->count();

	}

	public function user() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public function postBookmark() {

	    return $this->belongsTo(PostBookmark::class, 'post_id', 'post_id');
	 }

	public function postFiles() {

	   return $this->hasMany(PostFile::class, 'post_id');
	}

	public function postLikes() {

	   return $this->hasMany(PostLike::class, 'post_id');
	}

	public function postComments() {

	   return $this->hasMany(PostComment::class, 'post_id');
	}

	public function postBookmarks() {

	   return $this->hasMany(PostBookmark::class, 'post_id');
	}

	public function postPayments() {

	   return $this->hasMany(PostPayment::class, 'post_id');
	}

	public function reportPosts() {

		return $this->hasMany(ReportPost::class, 'post_id');
	 }

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaidApproved($query) {

        $query->where('posts.is_published', YES)->where('posts.status', YES)->where('posts.is_paid_post', YES)->where('posts.amount', '>', 0);

        return $query;

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('posts.is_published', YES)->where('posts.status', YES);

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

        static::deleting(function ($model){

            $model->postLikes()->delete();

            $model->postComments()->delete();

            $model->postPayments()->delete();

            $model->postBookmarks()->delete();

			$model->postFiles()->delete();
			
			$model->reportPosts()->delete();
            
        });

    }
}
