<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_posts', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->nullable();
            $table->integer('post_id');
            $table->integer('block_by');            
            $table->text('reason')->nullable();
            $table->tinyInteger('status')->default(YES);
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
        Schema::dropIfExists('report_posts');
    }
}
