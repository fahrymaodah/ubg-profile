<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing columns to fakultas table
        Schema::table('fakultas', function (Blueprint $table) {
            $table->longText('tujuan')->nullable()->after('misi');
            $table->longText('struktur_organisasi')->nullable()->after('sejarah');
            $table->string('struktur_image')->nullable()->after('struktur_organisasi');
        });

        // Add missing columns to prodi table
        Schema::table('prodi', function (Blueprint $table) {
            $table->longText('sejarah')->nullable()->after('tujuan');
            $table->longText('struktur_organisasi')->nullable()->after('sejarah');
            $table->string('struktur_image')->nullable()->after('struktur_organisasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fakultas', function (Blueprint $table) {
            $table->dropColumn(['tujuan', 'struktur_organisasi', 'struktur_image']);
        });

        Schema::table('prodi', function (Blueprint $table) {
            $table->dropColumn(['sejarah', 'struktur_organisasi', 'struktur_image']);
        });
    }
};
