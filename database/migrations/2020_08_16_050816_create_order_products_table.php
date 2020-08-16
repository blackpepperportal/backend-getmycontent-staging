<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('order_id');
            $table->integer('stardom_product_id');
            $table->float('quantity')->default(0.00);
            $table->float('per_quantity_price')->default(0.00);
            $table->float('sub_total')->default(0.00);
            $table->float('tax_price')->default(0.00);
            $table->float('delivery_price')->default(0.00);
            $table->float('total')->default(0.00);
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
        Schema::dropIfExists('order_products');
    }
}
