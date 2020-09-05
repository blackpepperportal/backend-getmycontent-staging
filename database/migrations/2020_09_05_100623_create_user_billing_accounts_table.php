<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBillingAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('user_billing_accounts')) {

            Schema::create('user_billing_accounts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('unique_id')->unique();
                $table->integer('user_id');
                $table->string('nickname')->nullable();
                $table->string('bank_name');
                $table->string('account_holder_name');
                $table->string('account_number');
                $table->string('ifsc_code');
                $table->string('swift_code')->nullable();
                $table->tinyInteger('is_default')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            
            });
            
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_billing_accounts');
    }
}
