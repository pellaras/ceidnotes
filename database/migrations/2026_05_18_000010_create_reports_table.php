<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('legacy_id')->nullable()->default(null)->unique();
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->index();
            $table->string('email')->nullable()->default(null);
            $table->morphs('reportable');
            $table->text('reason');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
