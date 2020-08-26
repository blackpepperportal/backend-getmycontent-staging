<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->string('title');
            $table->text('description');
            $table->float('amount')->default(0.00);
            $table->integer('plan')->default(1);
            $table->string('plan_type')->default(PLAN_TYPE_MONTH);
            $table->tinyInteger('is_free')->default(0);
            $table->tinyInteger('is_popular')->default(0);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('subscriptions');
    }
}

