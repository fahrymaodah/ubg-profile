<?php

namespace App\Filament\Widgets;

use App\Enums\ArticleStatus;
use App\Enums\UnitType;
use App\Models\Article;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestArticlesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Artikel Terbaru';

    public function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->query(
                Article::query()
                    ->when(!$user->isSuperAdmin(), function ($query) use ($user) {
                        if ($user->isUniversitas()) {
                            return $query->where('unit_type', UnitType::UNIVERSITAS);
                        }
                        return $query->where('unit_type', $user->unit_type)
                                     ->where('unit_id', $user->unit_id);
                    })
                    ->where('status', ArticleStatus::PUBLISHED)
                    ->latest('published_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Gambar')
                    ->square(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge(),

                Tables\Columns\TextColumn::make('views')
                    ->label('Views')
                    ->numeric(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Dipublikasikan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Article $record) => route('filament.admin.resources.articles.view', $record)),
            ])
            ->paginated(false);
    }
}
