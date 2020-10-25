<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostFile extends Model
{
    protected $fillable = ['file', 'post_id'];

    protected $hidden = ['deleted_at', 'id', 'unique_id'];

	protected $appends = ['post_file_id', 'post_file_unique_id'];

    public function getPostFileIdAttribute() {

        return $this->id;
    }

    public function getPostFileUniqueIdAttribute() {

        return $this->unique_id;
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PF"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PF"."-".$model->attributes['id']."-".uniqid();

            $model->file_type = get_post_file_type($model->attributes['file']);

            $model->save();
        
        });

    }
}
