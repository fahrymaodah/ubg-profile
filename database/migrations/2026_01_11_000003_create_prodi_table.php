<?php

use App\Enums\Jenjang;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fakultas_id')->constrained('fakultas')->cascadeOnDelete();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('subdomain')->unique();
            $table->string('kode', 10)->nullable();
            $table->string('jenjang')->default(Jenjang::S1->value);
            $table->text('deskripsi')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('tujuan')->nullable();
            $table->text('profil_lulusan')->nullable();
            $table->text('kompetensi')->nullable();
            $table->string('akreditasi', 50)->nullable();
            $table->string('no_sk_akreditasi', 100)->nullable();
            $table->date('tanggal_akreditasi')->nullable();
            $table->string('kurikulum_file')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('social_media')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->text('coming_soon_message')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('fakultas_id');
            $table->index('jenjang');
            $table->index('is_active');
            $table->index('is_published');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodi');
    }
};
