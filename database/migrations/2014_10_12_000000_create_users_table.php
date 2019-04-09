<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->unsignedInteger('legacy_id')->unique()->nullable()->default(null);
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('name');
            $table->string('AM')->unique();
            $table->unsignedInteger('registration_year');
            $table->boolean('send_results_by_email')->default(true);
            $table->unsignedInteger('phone_id')->nullable()->default(null);
            $table->unsignedInteger('phone_notifications_start')->default(10);
            $table->unsignedInteger('phone_notifications_end')->default(21);
            $table->string('password')->nullable()->default(null);
            $table->string('password_old')->nullable()->default(null);
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
            $table->softDeletes();
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
