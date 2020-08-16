<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_payments', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('user_id');
            $table->integer('post_id');
            $table->string('payment_id');
            $table->string('payment_mode')->default(CARD);
            $table->string('currency')->default('$');
            $table->float('paid_amount')->default(0.00);
            $table->dateTime('paid_date')->nullable();
            $table->tinyInteger('is_failed')->default(0);
            $table->tinyInteger('failed_reason')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('post_payments');
    }
}
