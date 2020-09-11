<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{	

	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];

    protected $appends = ['static_page_id','page_type'];

    public function getStaticPageIdAttribute() {

        return $this->id;
    }

    public function getPageTypeAttribute() {

        return $this->type;
    }

    /**
     * Get the Approved details 
     */
    public function scopeApproved($query) {
        
        return $query->where('static_pages.status' , APPROVED);	
    }
}
