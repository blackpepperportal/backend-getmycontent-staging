<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_cards', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(uniqid());
            $table->integer('user_id');
            $table->string('card_holder_name')->default("");
            $table->string('card_type');
            $table->string('customer_id');
            $table->string('last_four');
            $table->string('card_token');
            $table->integer('is_default')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_cards');
    }
}
