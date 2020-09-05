<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWalletPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('user_wallet_payments')) {

            Schema::create('user_wallet_payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('to_user_id')->default(0);
                $table->integer('received_from_user_id')->default(0);
                $table->integer('generated_invoice_id')->default(0);
                $table->string('payment_id');
                $table->string('payment_type')->default(WALLET_PAYMENT_TYPE_ADD)->comment("add, paid, credit");
                $table->string('amount_type')->default(WALLET_PAYMENT_TYPE_ADD)->comment("add, minus");
                $table->float('requested_amount')->default(0.00);
                $table->float('paid_amount')->default(0.00);
                $table->string('currency')->default('$');
                $table->string('payment_mode')->default(CARD);
                $table->dateTime('paid_date')->nullable();
                $table->string('message')->default("");
                $table->integer('is_cancelled')->default(0);
                $table->string('cancelled_reason')->default("");
                $table->string('updated_by')->default('user')->comment('admin, user');
                $table->string('bank_statement_picture')->default('');
                $table->tinyInteger('is_admin_approved')->default(0);
                $table->integer('user_billing_account_id')->default(0);
                $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('user_wallet_payments');
    }
}
