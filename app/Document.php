<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
   protected $hidden = ['deleted_at', 'id', 'unique_id'];

	protected $appends = ['document_id', 'document_unique_id'];

    public function getDocumentIdAttribute() {

        return $this->id;
    }

    public function getDocumentUniqueIdAttribute() {

        return $this->unique_id;
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query;
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        return $query->where('documents.status', APPROVED);
    
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "KV"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "KV"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
