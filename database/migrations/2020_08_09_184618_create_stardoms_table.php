<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStardomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stardoms', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->string('name');
            $table->string('first_name')->default('');
            $table->string('middle_name')->default('');
            $table->string('last_name')->default('');
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->text('about')->nullable();
            $table->enum('gender',['male','female','others'])->default('male');
            $table->string('picture')->default(asset('placeholder.jpg'));
            $table->string('password');
            $table->string('mobile');
            $table->string('address')->default('');
            $table->string('payment_mode')->default(CARD);
            $table->string('token');
            $table->string('token_expiry');
            $table->string('social_unique_id')->default('');
            $table->string('device_token')->nullable();
            $table->enum('device_type', ['web', 'android', 'ios'])->default('web');
            $table->enum('login_by', ['manual','facebook','google', 'instagram', 'apple', 'linkedin'])->default('manual');
            $table->tinyInteger('registration_steps')->default(0);
            $table->integer('push_notification_status')->default(YES);
            $table->integer('email_notification_status')->default(YES);
            $table->integer('is_verified')->default(0);
            $table->string('verification_code')->default('');
            $table->string('verification_code_expiry')->default('');
            $table->timestamp('email_verified_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('stardoms');
    }
}
