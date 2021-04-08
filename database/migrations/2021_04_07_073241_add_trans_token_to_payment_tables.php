<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransTokenToPaymentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_subscription_payments', function (Blueprint $table) {
            $table->string('trans_token')->default("");
        });

        Schema::table('user_tips', function (Blueprint $table) {
            $table->string('trans_token')->default("");
        });

        Schema::table('post_payments', function (Blueprint $table) {
            $table->string('trans_token')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_subscription_payments', function (Blueprint $table) {
            $table->dropColumn('trans_token');
        });

        Schema::table('user_tips', function (Blueprint $table) {
            $table->dropColumn('trans_token');
        });

        Schema::table('post_payments', function (Blueprint $table) {
            $table->dropColumn('trans_token');
        });
    }
}
