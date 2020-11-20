<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unique_id')->default(rand());
            $table->string('name');
            $table->string('first_name')->default('');
            $table->string('middle_name')->default('');
            $table->string('last_name')->default('');
            $table->string('username')->nullable();
            $table->string('email')->unique();
            $table->text('about')->nullable();
            $table->enum('gender',['male','female','others'])->default('male');
            $table->string('cover')->default(asset('cover.jpg'));
            $table->string('picture')->default(asset('placeholder.jpeg'));
            $table->string('password');
            $table->string('mobile');
            $table->string('address')->default('');
            $table->string('website')->default('');
            $table->string('amazon_wishlist')->default('');
            $table->tinyInteger('user_type')->default(0);
            $table->tinyInteger('user_account_type')->default(USER_FREE_ACCOUNT);
            $table->tinyInteger('is_document_verified')->default(0);
            $table->string('payment_mode')->default(CARD);
            $table->string('token');
            $table->string('token_expiry');
            $table->string('device_token')->nullable();
            $table->enum('device_type', ['web', 'android', 'ios'])->default('web');
            $table->enum('login_by', ['manual','facebook','google', 'instagram', 'apple', 'linkedin'])->default('manual');
            $table->string('social_unique_id')->default('');
            $table->tinyInteger('registration_steps')->default(0);
            $table->tinyInteger('is_push_notification')->default(YES);
            $table->tinyInteger('is_email_notification')->default(YES);
            $table->integer('user_card_id')->default(0);
            $table->tinyInteger('is_email_verified')->default(0);
            $table->string('verification_code')->default('');
            $table->string('verification_code_expiry')->default('');
            $table->timestamp('email_verified_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('one_time_subscription')->comment("0 - Not Subscribed , 1 - Subscribed")->default(0);
            $table->float('amount_paid')->default(0);
            $table->dateTime('expiry_date')->nullable();
            $table->tinyInteger('no_of_days')->default(0);
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
        Schema::dropIfExists('users');
    }
}
