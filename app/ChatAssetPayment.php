<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatAssetPayment extends Model
{
    protected $hidden = ['id','unique_id'];

	protected $appends = ['chat_asset_payments_id', 'chat_asset_payments_unique_id'];

	public function getChatAssetPaymentsIdAttribute() {

		return $this->id;
	}

	public function getChatAssetPaymentsUniqueIdAttribute() {

		return $this->unique_id;
	}

    public function chatMessage() {

        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    public function chatAssets() {

	   return $this->hasMany(ChatAsset::class, 'chat_message_id');
	}

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CAP"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CAP"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
