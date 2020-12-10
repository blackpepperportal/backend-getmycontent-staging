<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{
    //

    protected $fillable = ['block_by', 'blocked_to','reason'];

    protected $hidden = ['id'];

	protected $appends = ['block_user_id'];
	
	public function getBlockUserIdAttribute() {

		return $this->id;
	}


	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('block_users.status', APPROVED);

        return $query;

    }

	
}
