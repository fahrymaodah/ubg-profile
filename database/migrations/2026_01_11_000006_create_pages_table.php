<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('unit_type')->default('universitas');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->string('template')->default('default');
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['unit_type', 'unit_id', 'slug']);
            $table->index(['unit_type', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
