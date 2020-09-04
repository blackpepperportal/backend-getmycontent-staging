<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $appends = ['amount_formatted','plan_type_formatted'];

    public function getAmountFormattedAttribute() {

    	return formatted_amount($this->amount);
    }

    public function getPlanTypeFormattedAttribute() {

    	return formatted_plan($this->plan, $this->plan_type);
    }
}
