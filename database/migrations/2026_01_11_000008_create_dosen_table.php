<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->string('nidn', 20)->unique()->nullable();
            $table->string('nip', 30)->nullable();
            $table->string('nama');
            $table->string('gelar_depan', 50)->nullable();
            $table->string('gelar_belakang', 100)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('foto')->nullable();
            $table->string('jabatan_fungsional', 100)->nullable();
            $table->string('jabatan_struktural', 100)->nullable();
            $table->string('golongan', 20)->nullable();
            $table->text('bidang_keahlian')->nullable();
            $table->json('pendidikan')->nullable();
            $table->json('penelitian')->nullable();
            $table->json('pengabdian')->nullable();
            $table->json('publikasi')->nullable();
            $table->string('sinta_id', 50)->nullable();
            $table->string('google_scholar_id', 50)->nullable();
            $table->string('scopus_id', 50)->nullable();
            $table->string('orcid', 50)->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('prodi_id');
            $table->index('is_active');
            $table->index('jabatan_fungsional');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
