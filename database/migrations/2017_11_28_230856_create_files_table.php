<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('legacy_id')->unique()->nullable()->default(null);
            $table->unsignedInteger('directory_id');
            $table->string('name');
            $table->string('type');
            $table->boolean('is_owned')->default(false);
            $table->text('comment')->nullable()->default(null);
            $table->unsignedInteger('size');
            $table->unsignedInteger('total_views')->default(0);
            $table->unsignedInteger('total_downloads')->default(0);
            $table->unsignedInteger('total_overall')->default(0);
            $table->unsignedInteger('votes_up')->default(0);
            $table->unsignedInteger('votes_down')->default(0);
            $table->string('path')->nullable()->default(null);
            $table->string('md5');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('deleted_by_user_id')->nullable()->default(null);
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
        Schema::dropIfExists('files');
    }
}
