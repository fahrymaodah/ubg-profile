<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default(UserRole::PRODI->value)->after('password');
            $table->string('unit_type')->nullable()->after('role');
            $table->unsignedBigInteger('unit_id')->nullable()->after('unit_type');
            $table->boolean('is_active')->default(true)->after('unit_id');

            $table->index(['unit_type', 'unit_id']);
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['unit_type', 'unit_id']);
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'unit_type', 'unit_id', 'is_active']);
        });
    }
};
