<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWalletPayment extends Model
{
    protected $hidden = ['deleted_at', 'id', 'unique_id'];

	protected $appends = ['user_wallet_payment_id','user_wallet_payment_unique_id', 'paid_amount_formatted', 'status_formatted', 'wallet_picture', 'received_from_username','requested_amount_formatted','admin_amount_formatted','user_amount_formatted'];

    public function getUserWalletPaymentIdAttribute() {

        return $this->id;
    }

    public function getUserWalletPaymentUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getPaidAmountFormattedAttribute() {

        return wallet_formatted_amount($this->paid_amount, $this->amount_type);
    }

    public function getRequestedAmountFormattedAttribute() {

        return formatted_amount($this->requested_amount);
    }

    public function getAdminAmountFormattedAttribute() {

        return formatted_amount($this->admin_amount);
    }

    public function getUserAmountFormattedAttribute() {

        return formatted_amount($this->user_amount);
    }

    public function getStatusFormattedAttribute() {

        return paid_status_formatted($this->status);
    }

    public function getWalletPictureAttribute() {

        return wallet_picture($this->amount_type);
    }

    // public function getUsernameAttribute() {

    //     $username = $this->toUser ? $this->toUser->name : "You";

    //     unset($this->toUser);

    //     return $username;
    // }

    public function getReceivedFromUsernameAttribute() {

        $username = $this->ReceivedFromUser ? $this->ReceivedFromUser->name : "";

        unset($this->ReceivedFromUser);

        return $username;
    }
    
    public function user() {
    	return $this->belongsTo('App\User','user_id');
    }

    public function toUser() {
        return $this->belongsTo('App\User','to_user_id');
    }

    public function ReceivedFromUser() {

        return $this->belongsTo('App\User', 'received_from_user_id');
    }

    public function billingaccountDetails() {
        return $this->belongsTo('App\UserBillingAccount','user_billing_account_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {
        return $query;
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "UW"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UW"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
