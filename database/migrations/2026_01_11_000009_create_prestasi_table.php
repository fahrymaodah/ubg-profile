<?php

use App\Enums\PrestasiKategori;
use App\Enums\PrestasiTingkat;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('unit_type')->default('universitas');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('tingkat')->default(PrestasiTingkat::LOKAL->value);
            $table->string('kategori')->default(PrestasiKategori::LAINNYA->value);
            $table->string('penyelenggara')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('peserta')->nullable();
            $table->string('pembimbing')->nullable();
            $table->string('foto')->nullable();
            $table->json('gallery')->nullable();
            $table->string('link', 500)->nullable();
            $table->string('sertifikat')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['unit_type', 'unit_id']);
            $table->index('tingkat');
            $table->index('kategori');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};
