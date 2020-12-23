<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBellNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bell_notifications', function (Blueprint $table) {
            $table->integer('post_id')->after('message')->default(0);
            $table->integer('post_comment_id')->after('post_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bell_notifications', function (Blueprint $table) {
            $table->dropColumn('post_id');
            $table->dropColumn('post_comment_id');
        });
    }
}
