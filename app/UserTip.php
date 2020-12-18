<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTip extends Model
{
    protected $fillable = ['to_user_id', 'from_user_id', 'amount'];

    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['user_tip_id', 'user_tip_unique_id', 'from_username', 'from_user_picture', 'to_username', 'to_user_picture', 'amount_formatted'];
	
	public function getUserTipIdAttribute() {

		return $this->id;
	}

	public function getUserTipUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getAmountFormattedAttribute() {

		return formatted_amount($this->amount);
	}

	public function getFromUsernameAttribute() {

		return $this->fromUser->name ?? "";
	}

	public function getFromUserPictureAttribute() {

		return $this->fromUser->picture ?? "";
	}

	public function getToUsernameAttribute() {

		return $this->toUser->name ?? "";
	}

	public function getToUserPictureAttribute() {

		return $this->toUser->picture ?? "";
	}

	public function fromUser() {

	   return $this->belongsTo(User::class, 'from_user_id');
	}

	public function toUser() {

	   return $this->belongsTo(User::class, 'to_user_id');
	}

	public function post() {

	   return $this->belongsTo(Post::class, 'post_id');
	}

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "UT-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UT-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });
	}
	

	 /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserPaid($query, $from_user_id, $to_user_id) {

        $query->where('user_tips.user_id', $from_user_id)->where('user_tips.to_user_id', $to_user_id)->where('user_tips.status', PAID);

        return $query;

    }
}
