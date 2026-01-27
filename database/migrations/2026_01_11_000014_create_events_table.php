<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('unit_type')->default('universitas');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->string('image')->nullable();
            $table->string('registration_link', 500)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['unit_type', 'unit_id']);
            $table->index('start_date');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
