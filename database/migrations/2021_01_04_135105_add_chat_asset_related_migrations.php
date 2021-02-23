<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChatAssetRelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->integer('is_file_uploaded')->default(NO);
            $table->float('amount')->default(0.00);
            $table->tinyInteger('is_paid')->default(NO);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('is_file_uploaded');
            $table->dropColumn('amount');
            $table->dropColumn('is_paid');
        });
    }
}
