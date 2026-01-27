<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('unit_type')->default('universitas');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->string('file')->nullable();
            $table->string('youtube_url', 500)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['unit_type', 'unit_id']);
            $table->index('type');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
