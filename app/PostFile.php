<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostFile extends Model
{
    protected $fillable = ['file', 'post_id'];

    protected $hidden = ['deleted_at', 'id', 'unique_id', 'file'];

	protected $appends = ['post_file_id', 'post_file_unique_id'];

    public function getPostFileIdAttribute() {

        return $this->id;
    }

    public function getPostFileUniqueIdAttribute() {

        return $this->unique_id;
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return 
            $query->select(
            'post_files.*',
            'post_files.file as post_file'
            );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlurResponse($query) {

        return $query->select(
            'post_files.*',
            'post_files.blur_file as post_file'
            );
    
    }


    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PF"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PF"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function($model) {
        
            Helper::storage_delete_file($model->file, POST_TEMP_PATH);

            Helper::storage_delete_file($model->file, POST_PATH);

            Helper::storage_delete_file($model->blur_file, POST_BLUR_PATH);

        });

    }
}
