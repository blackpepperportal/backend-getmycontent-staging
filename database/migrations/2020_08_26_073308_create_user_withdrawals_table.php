<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWithDrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('user_id');
            $table->string('payment_id')->default("");
            $table->string('payment_mode')->default(PAYMENT_OFFLINE);
            $table->float('requested_amount')->default(0.00);
            $table->float('paid_amount')->default(0.00);
            $table->text('cancel_reason')->nullable();
            $table->tinyInteger('status')->default(0)->comment("0 - pending, 1 - paid, 2 - rejected");
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
        Schema::dropIfExists('user_withdrawals');
    }
}