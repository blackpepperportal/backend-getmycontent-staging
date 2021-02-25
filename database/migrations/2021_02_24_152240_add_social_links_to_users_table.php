<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialLinksToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('instagram_link')->default("");
            $table->string('facebook_link')->default("");
            $table->string('twitter_link')->default("");
            $table->string('linkedin_link')->default("");
            $table->string('pinterest_link')->default("");
            $table->string('youtube_link')->default("");
            $table->string('twitch_link')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('instagram_link');
            $table->dropColumn('facebook_link');
            $table->dropColumn('twitter_link');
            $table->dropColumn('linkedin_link');
            $table->dropColumn('pinterest_link');
            $table->dropColumn('youtube_link');
            $table->dropColumn('twitch_link');
        });
    }
}
