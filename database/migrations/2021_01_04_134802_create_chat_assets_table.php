<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_assets', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('from_user_id');
            $table->integer('to_user_id');
            $table->integer('chat_message_id');
            $table->string('file');
            $table->string('file_type')->default(FILE_TYPE_IMAGE);
            $table->float('amount')->default(0.00);
            $table->tinyInteger('is_paid')->default(NO);
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
        Schema::dropIfExists('chat_assets');
    }
}
