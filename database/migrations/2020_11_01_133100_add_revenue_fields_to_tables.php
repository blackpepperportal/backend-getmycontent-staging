<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevenueFieldsToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_payments', function (Blueprint $table) {
            $table->float('admin_amount')->after('paid_amount')->default(0.00);
            $table->float('user_amount')->after('admin_amount')->default(0.00);
        });

        Schema::table('user_subscription_payments', function (Blueprint $table) {
            $table->float('admin_amount')->after('amount')->default(0.00);
            $table->float('user_amount')->after('admin_amount')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_payments', function (Blueprint $table) {
            $table->dropColumn('admin_amount');
            $table->dropColumn('user_amount');
        });

        Schema::table('user_subscription_payments', function (Blueprint $table) {
            $table->dropColumn('admin_amount');
            $table->dropColumn('user_amount');
        });
    }
}
