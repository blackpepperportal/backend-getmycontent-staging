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

    protected $appends = ['user_id', 'user_unique_id', 'is_notification', 'is_document_verified_formatted', 'total_followers', 'total_followings', 'user_account_type_formatted', 'total_posts', 'total_fav_users', 'total_bookmarks', 'is_subscription_enabled', 'share_link','orders_count'];

    public function getUserIdAttribute() {

        return $this->id;
    }

    public function getUserUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getIsNotificationAttribute() {

        return $this->is_email_notification ? YES : NO;
    }

    public function getIsSubscriptionEnabledAttribute() {

        if($this->is_document_verified && $this->has('userBillingAccounts') && $this->is_email_verified) {
            return YES;
        }

        return NO;
    }

    public function getIsDocumentVerifiedFormattedAttribute() {

        return user_document_status_formatted($this->is_document_verified);
    }

    public function getTotalFollowersAttribute() {

        $count = $this->followers->count();

        unset($this->followers);
        
        return $count;

    }

    public function getShareLinkAttribute() {

        $share_link = \Setting::get('frontend_url').$this->unique_id;
        
        return $share_link;

    }

    public function getTotalFollowingsAttribute() {

        $count = $this->followings->count();

        unset($this->followings);
        
        return $count;

    }

    public function getTotalPostsAttribute() {
        
        $count = $this->posts->count();

        unset($this->posts);
        
        return $count;

    }

    public function getTotalFavUsersAttribute() {
        
        $count = $this->favUsers->count();

        unset($this->favUsers);
        
        return $count;

    }

    public function getTotalBookmarksAttribute() {
        
        $count = $this->postBookmarks->count();

        unset($this->postBookmarks);
        
        return $count;

    }

    public function getOrdersCountAttribute() {

        $count = $this->orders->count();

        unset($this->orders);
        
        return $count;

    }

    public function getUserAccountTypeFormattedAttribute() {
        
        return user_account_type_formatted($this->user_account_type);

    }

    public function userBillingAccounts() {

        return $this->hasMany(UserBillingAccount::class, 'user_id');
    }

    public function userDocuments() {

        return $this->hasMany(UserDocument::class, 'user_id');
    }

    public function deliveryAddresses() {

        return $this->hasMany(DeliveryAddress::class,'user_id');
    }

    public function orderPayments() {

        return $this->hasMany(OrderPayment::class,'user_id');
    }

    public function posts() {

        return $this->hasMany(Post::class,'user_id');
    }

    public function postPayments() {

        return $this->hasMany(PostPayment::class,'user_id');
    }

    public function orders() {

        return $this->hasMany(Order::class,'user_id');
    }

    public function userWallets() {

        return $this->hasOne(UserWallet::class, 'user_id');
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
    public function userSubscription() {
        
        return $this->hasOne(UserSubscription::class, 'user_id');
    }

    public function followers() {
        
        return $this->hasMany(Follower::class, 'user_id');
    }

    public function followings() {
        
        return $this->hasMany(Follower::class, 'follower_id');
    }

    
    public function postBookmarks() {
        
        return $this->hasMany(PostBookmark::class, 'user_id');
    }

    public function favUsers() {
        
        return $this->hasMany(FavUser::class, 'user_id');
    }

    public function postLikes() {

        return $this->hasMany(PostLike::class,'user_id');
    }

    public function postAlbums() {

        return $this->hasMany(PostAlbum::class,'user_id');
    }

    public function postComments() {

        return $this->hasMany(PostComment::class,'user_id');
    }

    public function userTips() {

        return $this->hasMany(UserTip::class,'user_id');
    }

    public function supportTickets() {

        return $this->hasMany(SupportTicket::class,'user_id');
    }

    public function fromUserSubscriptionPayments() {

        return $this->hasMany(UserSubscriptionPayment::class,'from_user_id');
    }
    
    public function toUserSubscriptionPayments() {

        return $this->hasMany(UserSubscriptionPayment::class,'to_user_id');
    }

    public function reportPosts() {

        return $this->hasMany(ReportPost::class,'block_by');
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
    public function scopeDocumentVerified($query) {

        $query->where('users.is_document_verified', USER_DOCUMENT_APPROVED);

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

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOtherResponse($query) {

        return $query->select(
            'users.id as user_id',
            'users.unique_id as user_unique_id',
            'users.*'
            );
    }
    
    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['name'] = "";

            if($model->attributes['first_name'] && $model->attributes['last_name']) {

                $model->attributes['name'] = $model->attributes['first_name']." ".$model->attributes['last_name'];
            }

            $model->attributes['unique_id'] = $model->attributes['username'] = routefreestring(strtolower($model->attributes['name'] ?: rand(1,10000).rand(1,10000)));

            $model->attributes['is_email_verified'] = USER_EMAIL_VERIFIED;

            if (Setting::get('is_account_email_verification') == YES && envfile('MAIL_USERNAME') && envfile('MAIL_PASSWORD')) { 

                if($model->attributes['login_by'] == 'manual') {

                    $model->generateEmailCode();

                }

            }

            $model->attributes['payment_mode'] = COD;

            $model->attributes['token'] = Helper::generate_token();

            $model->attributes['token_expiry'] = Helper::generate_token_expiry();

            $model->attributes['status'] = USER_APPROVED;

            if(in_array($model->attributes['login_by'], ['facebook', 'google', 'apple', 'linkedin', 'instagram'] )) {
                
                $model->attributes['password'] = \Hash::make($model->attributes['social_unique_id']);
            }

        });

        static::created(function($model) {

            $model->attributes['user_account_type'] = USER_FREE_ACCOUNT;
            
            $model->attributes['user_account_type'] = USER_FREE_ACCOUNT;

            $model->attributes['is_email_notification'] = $model->attributes['is_push_notification'] = YES;

            $model->attributes['unique_id'] = "UID"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::updating(function($model) {

            // $model->attributes['first_name'] = $model->attributes['last_name'] = $model->attributes['name'];

        });

        static::deleting(function ($model){

            Helper::storage_delete_file($model->picture, PROFILE_PATH_USER);

            $model->userCards()->delete();

            $model->userDocuments()->delete();
            
            $model->userBillingAccounts()->delete();

            $model->postLikes()->delete();

            $model->postAlbums()->delete();

            $model->postComments()->delete();

            $model->userTips()->delete();
            
            foreach ($model->posts as $key => $post) {
                $post->delete();
            }

            $model->postPayments()->delete();

            foreach ($model->orders as $key => $order) {
                $order->delete();
            }

            $model->deliveryAddresses()->delete();

            $model->userWallets()->delete();
            
            $model->userWithdrawals()->delete();

            $model->followers()->delete();

            $model->followings()->delete();

            $model->postBookmarks()->delete();

            $model->favUsers()->delete();
            
            $model->userSubscription()->delete();
            
            $model->supportTickets()->delete();

            $model->fromUserSubscriptionPayments()->delete();
            
            $model->toUserSubscriptionPayments()->delete();

            $model->reportPosts()->delete();


            \App\ChatUser::where('from_user_id', $model->id)->orWhere('to_user_id', $model->id)->delete();

            \App\ChatMessage::where('from_user_id', $model->id)->orWhere('to_user_id', $model->id)->delete();
            
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

            if($this->attributes['login_by'] != 'manual') {

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
