<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $hidden = ['id','unique_id'];

	protected $appends = ['chat_message_id', 'chat_message_unique_id', 'from_username', 'from_displayname', 'from_userpicture', 'from_user_unique_id', 'to_username', 'to_displayname', 'to_userpicture', 'to_user_unique_id'];

	public function getChatMessageIdAttribute() {

		return $this->id;
	}

	public function getChatMessageUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getFromUsernameAttribute() {

		return $this->fromUser->username ?? tr('n_a');
	}

	public function getFromUserPictureAttribute() {

		return $this->fromUser->picture ?? asset('placeholder.jpeg');
	}

	public function getFromDisplaynameAttribute() {

		return $this->fromUser->name ?? tr('n_a');
	}

	public function getFromUserUniqueIdAttribute() {

		return $this->fromUser->unique_id ?? '';
	}

	public function getToUsernameAttribute() {

		return $this->toUser->username ?? tr('n_a');
	}

	public function getToUserPictureAttribute() {

		return $this->toUser->picture ?? asset('placeholder.jpeg');
	}

	public function getToDisplaynameAttribute() {

		return $this->toUser->name ?? tr('n_a');
	}

	public function getToUserUniqueIdAttribute() {

		return $this->toUser->unique_id ?? '';
	}

	public function fromUser() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public function toUser() {

	   return $this->belongsTo(User::class, 'to_user_id');
	}

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CM"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CM"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
