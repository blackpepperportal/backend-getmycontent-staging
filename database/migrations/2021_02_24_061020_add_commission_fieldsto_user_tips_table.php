<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionFieldstoUserTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_tips', function (Blueprint $table) {
            $table->float('admin_amount')->default(0.00)->after('currency');
            $table->float('user_amount')->default(0.00)->after('admin_amount');

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('user_tips', function (Blueprint $table) {
            $table->dropColumn('admin_amount');
            $table->dropColumn('user_amount');
        });
    }
}
