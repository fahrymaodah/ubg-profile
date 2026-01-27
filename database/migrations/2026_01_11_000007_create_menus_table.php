<?php

use App\Enums\MenuType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('unit_type')->default('universitas');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('title');
            $table->string('type')->default(MenuType::LINK->value);
            $table->string('url', 500)->nullable();
            $table->foreignId('article_id')->nullable()->constrained('articles')->nullOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('target')->default('_self');
            $table->string('icon', 100)->nullable();
            $table->string('css_class')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('menus')->cascadeOnDelete();
            $table->index(['unit_type', 'unit_id']);
            $table->index('parent_id');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
