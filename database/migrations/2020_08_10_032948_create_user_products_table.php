<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_products', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('user_id');
            $table->integer('category_id');
            $table->integer('sub_category_id');
            $table->string('name');
            $table->text('description');
            $table->string('picture')->default(asset('product-placeholder.jpeg'));
            $table->float('quantity')->default(0.00);
            $table->float('price')->default(0.00);
            $table->float('delivery_price')->default(0.00);
            $table->tinyInteger('is_outofstock')->default(PRODUCT_AVAILABLE);
            $table->tinyInteger('is_visible')->default(YES);
            $table->tinyInteger('status')->default(APPROVED);
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
        Schema::dropIfExists('user_products');
    }
}
