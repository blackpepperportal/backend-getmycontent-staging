<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unique_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('about')->nullable();
            $table->string('picture')->default(asset('placeholder.jpeg'));
            $table->string('timezone')->default("");
            $table->enum('gender', ['male', 'female', 'others'])->default('male');
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
        Schema::dropIfExists('admins');
    }
}
