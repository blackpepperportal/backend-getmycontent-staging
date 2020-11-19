<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_members', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->string('name');
            $table->string('first_name')->default('');
            $table->string('middle_name')->default('');
            $table->string('last_name')->default('');
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->text('about')->nullable();
            $table->string('picture')->default(asset('placeholder.jpeg'));
            $table->string('password');
            $table->string('mobile');
            $table->string('address')->default('');
            $table->string('token');
            $table->string('token_expiry');
            $table->string('device_token')->nullable();
            $table->enum('device_type', ['web', 'android', 'ios'])->default('web');
            $table->integer('is_email_verified')->default(YES);
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
        Schema::dropIfExists('support_members');
    }
}
