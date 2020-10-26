<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscriptionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(uniqid());
            $table->integer('from_user_id');
            $table->integer('to_user_id');
            $table->string('payment_id')->default("");
            $table->float('amount')->default(0.00);
            $table->string('payment_mode')->default(COD);
            $table->integer('is_current_subscription')->default(0);
            $table->datetime('expiry_date')->nullable();
            $table->datetime('paid_date')->nullable();
            $table->tinyInteger('status')->default(PAID);
            $table->tinyInteger('is_cancelled')->default(0);
            $table->text('cancel_reason')->nullable("");
            $table->integer('plan')->default(1);
            $table->string('plan_type')->default(PLAN_TYPE_MONTH);
            $table->softDeletes();
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
        Schema::dropIfExists('user_subscription_payments');
    }
}
