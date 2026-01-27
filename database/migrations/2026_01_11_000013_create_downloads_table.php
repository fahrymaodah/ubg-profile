<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('unit_type')->default('universitas');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedBigInteger('download_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['unit_type', 'unit_id']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
