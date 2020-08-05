<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('static_pages')) {

            Schema::create('static_pages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('unique_id')->default(uniqid());
                $table->string('title')->unique();
                $table->text('description');
                $table->enum('type',['about','privacy','terms','refund','cancellation','faq','help','contact','others'])->default('others');
                $table->string('section_type')->nullable();
                $table->tinyInteger('status')->default(APPROVED);
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
        Schema::dropIfExists('static_pages');
    }
}
