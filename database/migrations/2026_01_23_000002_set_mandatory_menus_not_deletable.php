<?php

use App\Enums\UnitType;
use App\Models\Menu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Mandatory menu titles (lowercase)
     */
    protected array $mandatoryMenus = ['beranda', 'profil', 'akademik', 'kontak'];

    public function up(): void
    {
        // Update existing mandatory menus to is_deletable = false
        Menu::whereNull('parent_id')
            ->whereIn(\DB::raw('LOWER(title)'), $this->mandatoryMenus)
            ->update(['is_deletable' => false]);

        // Update non-mandatory menus to is_deletable = true (in case any have null)
        Menu::whereNull('is_deletable')
            ->update(['is_deletable' => true]);
    }

    public function down(): void
    {
        // Reset all menus to deletable
        Menu::query()->update(['is_deletable' => true]);
    }
};
