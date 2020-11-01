<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $hidden = ['id','unique_id'];

	protected $appends = ['chat_message_id', 'chat_message_unique_id', 'from_username', 'from_displayname', 'from_userpicture', 'to_username', 'to_displayname', 'to_userpicture'];

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

	public function getFromUserdisplaynameAttribute() {

		return $this->fromUser->name ?? tr('n_a');
	}

	public function getToUsernameAttribute() {

		return $this->toUser->username ?? tr('n_a');
	}

	public function getToUserPictureAttribute() {

		return $this->toUser->picture ?? asset('placeholder.jpeg');
	}

	public function getToUserdisplaynameAttribute() {

		return $this->toUser->name ?? tr('n_a');
	}

	public function fromUser() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public function toUser() {

	   return $this->belongsTo(User::class, 'to_user_id');
	}
}
