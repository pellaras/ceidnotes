<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('legacy_id')->unique()->nullable();
            $table->foreignId('directory_id');
            $table->string('name');
            $table->string('type');
            $table->boolean('is_owned')->default(false);
            $table->text('comment')->nullable();
            $table->unsignedInteger('size');
            $table->unsignedInteger('total_views')->default(0);
            $table->unsignedInteger('total_downloads')->default(0);
            $table->unsignedInteger('total_overall')->default(0);
            $table->unsignedInteger('votes_up')->default(0);
            $table->unsignedInteger('votes_down')->default(0);
            $table->string('path', 1020)->nullable();
            $table->string('md5');
            $table->foreignId('user_id');
            $table->foreignId('deleted_by_user_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
