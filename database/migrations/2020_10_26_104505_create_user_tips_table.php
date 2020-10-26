<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tips', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('user_id');
            $table->integer('to_user_id');
            $table->integer('post_id')->default(0);
            $table->float('amount')->default(0.00);
            $table->string('payment_id');
            $table->integer('user_card_id')->default(0);
            $table->string('payment_mode')->default(CARD);
            $table->string('currency')->default('$');
            $table->dateTime('paid_date')->nullable();
            $table->tinyInteger('is_failed')->default(0);
            $table->tinyInteger('failed_reason')->default(0);
            $table->tinyInteger('status')->default(APPROVED);
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
        Schema::dropIfExists('user_tips');
    }
}
