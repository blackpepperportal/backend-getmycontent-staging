<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Setting, DB;

use App\Helpers\Helper;

class User extends Authenticatable
{
    use Notifiable;

    protected $appends = ['user_id', 'is_notification'];

    public function getUserIdAttribute() {

        return $this->id;
    }

    public function getIsNotificationAttribute() {

        return $this->is_email_notification ? YES : NO;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function deliveryAddresses() {

        return $this->hasMany(DeliveryAddress::class,'user_id');
    }

    public function orderPayments() {

        return $this->hasMany(OrderPayment::class,'user_id');
    }

    public function postPayments() {

        return $this->hasMany(PostPayment::class,'user_id');
    }

    public function orders() {

        return $this->hasMany(Order::class,'user_id');
    }

    public function userWallets() {

        return $this->hasMany(UserWallet::class, 'user_id');
    }

    public function userWithdrawals() {

        return $this->hasMany(UserWithdrawal::class,'user_id');
    }

    /**
     * Get the UserCard record associated with the user.
     */
    public function userCards() {
        
        return $this->hasMany(UserCard::class, 'user_id');
    }

    /**
     * Get the UserCard record associated with the user.
     */
    public function scopeIsContentCreator($query, $status) {
        
        return $query->where('is_content_creator',$status);
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('users.status', USER_APPROVED)->where('is_email_verified', USER_EMAIL_VERIFIED);

        return $query;

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->select(
            'users.id as user_id',
            'users.unique_id as user_unique_id',
            'users.*'
            );
    }
    
    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['name'] = $model->attributes['first_name']." ".$model->attributes['last_name'];

            $model->attributes['is_email_verified'] = USER_EMAIL_VERIFIED;

            if (Setting::get('is_account_email_verification') == YES && env('MAIL_USERNAME') && env('MAIL_PASSWORD')) { 

                if($model->attributes['login_by'] == 'manual') {

                    $model->generateEmailCode();

                }

            }

            $model->attributes['payment_mode'] = COD;

            $model->attributes['username'] = routefreestring($model->attributes['name']);

            $model->attributes['unique_id'] = uniqid();

            $model->attributes['token'] = Helper::generate_token();

            $model->attributes['token_expiry'] = Helper::generate_token_expiry();

            $model->attributes['status'] = USER_APPROVED;

            if(in_array($model->attributes['login_by'], ['facebook', 'google', 'apple', 'linkedin', 'instagram'] )) {
                
                $model->attributes['password'] = \Hash::make($model->attributes['social_unique_id']);
            }

        });

        static::created(function($model) {

            $model->attributes['is_email_notification'] = $model->attributes['is_push_notification'] = YES;

            $model->attributes['unique_id'] = "UID"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::updating(function($model) {

            $model->attributes['username'] = routefreestring($model->attributes['name']);

            $model->attributes['first_name'] = $model->attributes['last_name'] = $model->attributes['name'];

        });

        static::deleting(function ($model){

            Helper::delete_file($model->picture , PROFILE_PATH_USER);

            $model->deliveryAddresses()->delete();

            $model->orders()->delete();

            $model->orderPayments()->delete();

            $model->postPayments()->delete();

            $model->userWallets()->delete();
            
            $model->userWithdrawals()->delete();
        });

    }

    /**
     * Generates Token and Token Expiry
     * 
     * @return bool returns true if successful. false on failure.
     */

    protected function generateEmailCode() {

        $this->attributes['verification_code'] = Helper::generate_email_code();

        $this->attributes['verification_code_expiry'] = Helper::generate_email_expiry();

        // Check Email verification controls and email configurations

        if(Setting::get('is_account_email_verification') == YES && Setting::get('is_email_notification') == YES && Setting::get('is_email_configured') == YES) {

            if($model->attributes['login_by'] != 'manual') {

                $this->attributes['is_email_verified'] = USER_EMAIL_VERIFIED;

            } else {

                $this->attributes['is_email_verified'] = USER_EMAIL_NOT_VERIFIED;
            }

        } else { 

            $this->attributes['is_email_verified'] = USER_EMAIL_VERIFIED;
        }

        return true;
    
    }
}
