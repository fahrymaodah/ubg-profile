<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use App\Models\Prodi;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Pesan Kontak';

    protected static ?string $modelLabel = 'Pesan Kontak';

    protected static ?string $pluralModelLabel = 'Pesan Kontak';

    protected static ?int $navigationSort = 8;

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $query = static::getModel()::query()->where('status', 'unread');

        // Apply same scoping as getEloquentQuery
        $count = match ($user->role) {
            UserRole::SUPERADMIN, UserRole::UNIVERSITAS => $query->count(),
            UserRole::FAKULTAS => $query->where(function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('unit_type', UnitType::FAKULTAS)
                       ->where('unit_id', $user->unit_id);
                })->orWhere(function ($q2) use ($user) {
                    $q2->where('unit_type', UnitType::PRODI)
                       ->whereIn('unit_id', Prodi::where('fakultas_id', $user->unit_id)->pluck('id'));
                });
            })->count(),
            UserRole::PRODI => $query->where('unit_type', UnitType::PRODI)
                                     ->where('unit_id', $user->unit_id)->count(),
            default => 0,
        };

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengirim')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->disabled(),

                        TextInput::make('email')
                            ->label('Email')
                            ->disabled(),

                        TextInput::make('phone')
                            ->label('Telepon')
                            ->disabled()
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make('Pesan')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Subjek')
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->label('Isi Pesan')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'unread' => 'Belum Dibaca',
                                'read' => 'Sudah Dibaca',
                                'replied' => 'Sudah Dibalas',
                            ])
                            ->required(),

                        Placeholder::make('replied_info')
                            ->label('Informasi Balasan')
                            ->content(function ($record) {
                                if ($record && $record->status === 'replied') {
                                    $repliedBy = $record->repliedByUser?->name ?? 'Unknown';
                                    $repliedAt = $record->replied_at?->format('d M Y H:i') ?? '-';
                                    return "Dibalas oleh {$repliedBy} pada {$repliedAt}";
                                }
                                return 'Belum dibalas';
                            })
                            ->visible(fn ($record) => $record !== null),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email disalin'),

                TextColumn::make('subject')
                    ->label('Subjek')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'danger',
                        'read' => 'warning',
                        'replied' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unread' => 'Belum Dibaca',
                        'read' => 'Sudah Dibaca',
                        'replied' => 'Sudah Dibalas',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Diterima')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('replied_at')
                    ->label('Dibalas')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'unread' => 'Belum Dibaca',
                        'read' => 'Sudah Dibaca',
                        'replied' => 'Sudah Dibalas',
                    ]),
            ])
            ->actions([
                Action::make('markAsRead')
                    ->label('Tandai Dibaca')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->visible(fn (ContactMessage $record) => $record->status === 'unread')
                    ->action(function (ContactMessage $record) {
                        $record->markAsRead();
                        Notification::make()
                            ->title('Pesan ditandai sebagai dibaca')
                            ->success()
                            ->send();
                    }),

                Action::make('reply')
                    ->label('Balas')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->url(fn (ContactMessage $record) => "mailto:{$record->email}?subject=Re: {$record->subject}")
                    ->openUrlInNewTab()
                    ->after(function (ContactMessage $record) {
                        $record->markAsReplied();
                    }),

                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('markAsRead')
                        ->label('Tandai Dibaca')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->markAsRead());
                            Notification::make()
                                ->title('Pesan ditandai sebagai dibaca')
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Messages are created via frontend contact form
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        return match ($user->role) {
            UserRole::SUPERADMIN => $query,
            UserRole::UNIVERSITAS => $query->where('unit_type', UnitType::UNIVERSITAS),
            UserRole::FAKULTAS => $query->where('unit_type', UnitType::FAKULTAS)
                                        ->where('unit_id', $user->unit_id),
            UserRole::PRODI => $query->where('unit_type', UnitType::PRODI)
                                     ->where('unit_id', $user->unit_id),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
