<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatAssetPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_asset_payments', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('from_user_id');
            $table->integer('to_user_id');
            $table->integer('chat_message_id');
            $table->integer('user_card_id')->default(0);
            $table->string('payment_id');
            $table->string('payment_mode')->default(CARD);
            $table->string('currency')->default('$');
            $table->float('paid_amount')->default(0.00);
            $table->dateTime('paid_date')->nullable();
            $table->tinyInteger('is_failed')->default(0);
            $table->tinyInteger('failed_reason')->default(0);
            $table->float('admin_amount')->default(0.00);
            $table->float('user_amount')->default(0.00);
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
        Schema::dropIfExists('chat_asset_payments');
    }
}
