<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UCategory extends Model
{
	protected $hidden = ['id', 'unique_id'];

	protected $appends = ['u_category_id','u_category_unique_id','total_users'];

    public function getUCategoryIdAttribute() {

        return $this->id;
    }

    public function getUCategoryUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getTotalUsersAttribute() {
        
        return $this->userCategories()->whereHas('user')->count();
    }

    public function userCategories() {

        return $this->hasMany(UserCategory::class,'u_category_id');
    }

    /**
     * Scope a query to only include active members.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('u_categories.status', APPROVED);

        return $query;

    }
    
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query;
    
    }
}
