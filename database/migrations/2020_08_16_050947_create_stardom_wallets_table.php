<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStardomWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stardom_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('stardom_id');
            $table->float('total')->default(0.00);
            $table->float('onhold')->default(0.00);
            $table->float('used')->default(0.00);
            $table->float('remaining')->default(0.00);
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
        Schema::dropIfExists('stardom_wallets');
    }
}
