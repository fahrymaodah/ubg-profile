<?php

namespace App\Filament\Widgets;

use App\Enums\ArticleStatus;
use App\Enums\UnitType;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Dosen;
use App\Models\Event;
use App\Models\Fakultas;
use App\Models\Prodi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $stats = [];

        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            // Stats for universitas level
            $stats[] = Stat::make('Fakultas', Fakultas::where('is_active', true)->count())
                ->description('Fakultas aktif')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary');

            $stats[] = Stat::make('Program Studi', Prodi::where('is_active', true)->count())
                ->description('Prodi aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success');

            $stats[] = Stat::make('Dosen', Dosen::where('is_active', true)->count())
                ->description('Dosen aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info');

            $stats[] = Stat::make('Artikel', Article::where('status', ArticleStatus::PUBLISHED)->count())
                ->description('Artikel dipublikasikan')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('warning');
        } elseif ($user->isFakultas() && $user->unit_id) {
            // Stats for fakultas level
            $fakultas = Fakultas::find($user->unit_id);
            $prodiIds = Prodi::where('fakultas_id', $user->unit_id)->pluck('id');

            $stats[] = Stat::make('Program Studi', Prodi::where('fakultas_id', $user->unit_id)->where('is_active', true)->count())
                ->description('Prodi di fakultas ini')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success');

            $stats[] = Stat::make('Dosen', Dosen::whereIn('prodi_id', $prodiIds)->where('is_active', true)->count())
                ->description('Dosen di fakultas ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info');

            $stats[] = Stat::make('Artikel', Article::where('unit_type', UnitType::FAKULTAS)
                    ->where('unit_id', $user->unit_id)
                    ->where('status', ArticleStatus::PUBLISHED)
                    ->count())
                ->description('Artikel dipublikasikan')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('warning');

            $stats[] = Stat::make('Pesan Kontak', ContactMessage::where('unit_type', UnitType::FAKULTAS)
                    ->where('unit_id', $user->unit_id)
                    ->where('status', 'unread')
                    ->count())
                ->description('Pesan belum dibaca')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger');
        } elseif ($user->isProdi() && $user->unit_id) {
            // Stats for prodi level
            $stats[] = Stat::make('Dosen', Dosen::where('prodi_id', $user->unit_id)->where('is_active', true)->count())
                ->description('Dosen di prodi ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info');

            $stats[] = Stat::make('Artikel', Article::where('unit_type', UnitType::PRODI)
                    ->where('unit_id', $user->unit_id)
                    ->where('status', ArticleStatus::PUBLISHED)
                    ->count())
                ->description('Artikel dipublikasikan')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('warning');

            $stats[] = Stat::make('Pesan Kontak', ContactMessage::where('unit_type', UnitType::PRODI)
                    ->where('unit_id', $user->unit_id)
                    ->where('status', 'unread')
                    ->count())
                ->description('Pesan belum dibaca')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger');

            $stats[] = Stat::make('Event', Event::where('unit_type', UnitType::PRODI)
                    ->where('unit_id', $user->unit_id)
                    ->where('start_date', '>=', now())
                    ->count())
                ->description('Event mendatang')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success');
        }

        return $stats;
    }
}
