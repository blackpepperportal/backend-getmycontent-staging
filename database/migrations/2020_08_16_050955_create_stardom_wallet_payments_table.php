<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStardomWalletPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stardom_wallet_payments', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('stardom_id');
            $table->string('payment_id');
            $table->string('payment_type')->default('add')->comment("add, paid, credit");
            $table->string('amount_type')->default('add')->comment("add, minus");
            $table->float('requested_amount')->default(0.00);
            $table->float('paid_amount')->default(0.00);
            $table->string('currency')->default('$');
            $table->string('payment_mode')->default(CARD);
            $table->dateTime('paid_date')->nullable();
            $table->string('message')->default("");
            $table->integer('is_failed')->default(0);
            $table->string('failed_reason')->default("");
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
        Schema::dropIfExists('stardom_wallet_payments');
    }
}
