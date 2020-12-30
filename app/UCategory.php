<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UCategory extends Model
{
    //

	protected $hidden = ['id', 'unique_id'];

	protected $appends = ['u_categories_id','u_categories_unique_id','users_count'];

    public function getUCategoriesIdAttribute() {

        return $this->id;
    }

    public function getUCategoriesUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function usercategory() {

        return $this->hasMany(UserCategory::class,'u_category_id');
    }

     public function getUsersCountAttribute() {
        
        return $this->usercategory()->count();
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
