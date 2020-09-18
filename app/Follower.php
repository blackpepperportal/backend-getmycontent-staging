<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    /**
     * Load follower using relation model
     */
    public function getUser()
    {
        return $this->hasOne('App\User', 'id', 'follower_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->leftJoin('users' , 'users.id' ,'=' , 'followers.follower_id')
			->select(
				'users.id as user_id',
	            'users.unique_id as user_unique_id',
                'users.username',
	            'users.email as email',
	            'users.picture as picture',
	            'users.is_content_creator',
                'followers.follower_id',
                'followers.created_at',
                'followers.updated_at'
            );
    
    }

    public function followerDetails() {

        return $this->belongsTo(User::class,'follower_id');
    }

    public function userDetails() {

        return $this->belongsTo(User::class,'user_id');
    }
}
