<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
	protected $hidden = ['deleted_at', 'id', 'unique_id'];

    protected $fillable = ['user_id', 'total', 'used', 'remaining'];

	protected $appends = ['user_wallet_id','user_wallet_unique_id', 'total_formatted', 'used_formatted', 'remaining_formatted'];

	public function getUserWalletIdAttribute() {

        return $this->id;
    }

    public function getUserWalletUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getTotalFormattedAttribute() {

        return formatted_amount($this->total);
    }

    public function getUsedFormattedAttribute() {

        return formatted_amount($this->used);
    }

     public function getOnHoldFormattedAttribute() {

        return formatted_amount($this->on_hold);
    }

    public function getRemainingFormattedAttribute() {

        return formatted_amount($this->remaining);
    }

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {
        return $query->join('users','users.id','=','user_wallets.user_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "UW-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UW-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });
    }
}
