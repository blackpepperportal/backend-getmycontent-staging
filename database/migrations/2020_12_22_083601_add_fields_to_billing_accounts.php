<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBillingAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_billing_accounts', function (Blueprint $table) {
            $table->string('iban_number')->default("")->before('status');
            $table->string('route_number')->default("")->after('iban_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_billing_accounts', function (Blueprint $table) {
            $table->dropColumn('iban_number');
            $table->dropColumn('route_number');
        });
    }
}
