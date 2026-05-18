<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->string('legacy_id')->unique()->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('likeable_id');
            $table->string('likeable_type');
            $table->tinyInteger('value')->index();
            $table->timestamps();
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
