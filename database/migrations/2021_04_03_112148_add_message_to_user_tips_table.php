<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMessageToUserTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_tips', function (Blueprint $table) {
            $table->text('message')->after('amount')->nullable();
            $table->integer('user_wallet_payment_id')->default(0);
        });

        Schema::table('user_wallet_payments', function (Blueprint $table) {
            $table->string('usage_type')->after('amount_type')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_tips', function (Blueprint $table) {
            $table->dropColumn('message');
        });
    }
}
