<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWithdrawal extends Model
{
    protected $hidden = ['deleted_at', 'id', 'unique_id'];

    protected $fillable = ['user_id', 'requested_amount'];

	protected $appends = ['user_withdrawal_id','user_withdrawal_unique_id', 'requested_amount_formatted', 'paid_amount_formatted', 'status_formatted', 'withdraw_picture', 'billing_account_name'];

	public function getUserWithdrawalIdAttribute() {

        return $this->id;
    }

    public function getUserWithdrawalUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getBillingAccountNameAttribute() {

        return $this->billingAccount->nickname ?? "-";
    }

    public function getRequestedAmountFormattedAttribute() {

        return formatted_amount($this->requested_amount);
    }

    public function getPaidAmountFormattedAttribute() {

        return formatted_amount($this->paid_amount);
    }

    public function getStatusFormattedAttribute() {

        return withdrawal_status_formatted($this->status);
    }

    public function getWithdrawPictureAttribute() {

        return withdraw_picture($this->status);
    }

    public function getCancelBtnStatusAttribute() {

        if(in_array($this->status, [WITHDRAW_INITIATED, WITHDRAW_ONHOLD])) {
            return YES;
        }

        return NO;
    }


    public function billingaccountDetails(){

        return $this->belongsTo(UserBillingAccount::class,'user_billing_account_id');
    }

    public function user() {
    	return $this->belongsTo('App\User','user_id');
    }

    public function scopeCommonResponse($query) {
        return $query
        ->join('users','users.id','=','user_withdrawals.user_id')
        ->select('user_withdrawals.*');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "WDR-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "WDR-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });
    }

    public function billingAccount() {
    	return $this->belongsTo(UserBillingAccount::class,'user_billing_account_id');
    }
}
