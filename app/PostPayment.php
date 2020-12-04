<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostPayment extends Model
{
	protected  $appends = ['paid_amount_formatted'];

    public function getPaidAmountFormattedAttribute() {

    	return formatted_amount($this->paid_amount);
    }

    public function user() {

    	return $this->belongsTo(User::class,'user_id');
    }

    public function postDetails() {

    	return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserPaid($query, $user_id, $post_id) {

        $query->where('post_payments.post_id', $post_id)->where('post_payments.user_id', $user_id)->where('post_payments.status', PAID);

        return $query;

    }
}
