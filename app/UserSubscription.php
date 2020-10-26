<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['user_subscription_id', 'user_subscription_unique_id', 'monthly_amount_formatted', 'yearly_amount_formatted', 'username', 'user_picture'];
	
	public function getUserSubscriptionIdAttribute() {

		return $this->id;
	}

	public function getUserSubscriptionUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getMonthlyAmountFormattedUniqueIdAttribute() {

		return formatted_amount($this->monthly_amount_formatted);
	}

	public function getYearlyAmountFormattedUniqueIdAttribute() {

		return formatted_amount($this->yearly_amount_formatted);
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

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('user_subscriptions.status', APPROVED);

        return $query;

    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "US-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "US-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });
    }
}
