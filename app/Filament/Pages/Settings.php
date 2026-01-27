<?php

namespace App\Filament\Pages;

use App\Enums\UnitType;
use App\Models\Setting;
use App\Services\SettingService;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings';

    protected static string | \UnitEnum | null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan Website';

    protected static ?string $title = 'Pengaturan Website';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        // Determine unit type and id based on user role
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            $unitType = UnitType::UNIVERSITAS;
            $unitId = null;
        } else {
            $unitType = $user->unit_type;
            $unitId = $user->unit_id;
        }

        // Load existing settings
        $existingSettings = Setting::query()
            ->where('unit_type', $unitType)
            ->where(function ($q) use ($unitId) {
                if ($unitId) {
                    $q->where('unit_id', $unitId);
                } else {
                    $q->whereNull('unit_id');
                }
            })
            ->pluck('value', 'key')
            ->toArray();

        // Convert boolean string values to actual booleans for toggles
        $booleanFields = [
            'show_announcement_bar',
            'show_floating_whatsapp',
            'show_back_to_top',
            'enable_dark_mode'
        ];

        foreach ($booleanFields as $field) {
            if (isset($existingSettings[$field])) {
                $existingSettings[$field] = $existingSettings[$field] === 'true' || $existingSettings[$field] === '1';
            }
        }

        $this->form->fill($existingSettings);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('Umum')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Identitas Website')
                                    ->schema([
                                        TextInput::make('site_name')
                                            ->label('Nama Website')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('site_description')
                                            ->label('Deskripsi Website')
                                            ->rows(3)
                                            ->maxLength(500),

                                        TextInput::make('site_keywords')
                                            ->label('Keywords (SEO)')
                                            ->maxLength(255)
                                            ->helperText('Pisahkan dengan koma'),
                                    ]),

                                Section::make('Logo & Favicon')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                FileUpload::make('logo')
                                                    ->label('Logo')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->maxSize(2048),

                                                FileUpload::make('logo_dark')
                                                    ->label('Logo (Dark Mode)')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->maxSize(2048),

                                                FileUpload::make('favicon')
                                                    ->label('Favicon')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->maxSize(512)
                                                    ->helperText('PNG, 32x32px atau 64x64px'),

                                                FileUpload::make('og_image')
                                                    ->label('OG Image (Social Share)')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->maxSize(2048)
                                                    ->helperText('1200x630px untuk social media sharing'),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Kontak')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('Informasi Kontak')
                                    ->schema([
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255),

                                        TextInput::make('phone')
                                            ->label('Telepon')
                                            ->tel()
                                            ->maxLength(20),

                                        TextInput::make('whatsapp')
                                            ->label('WhatsApp')
                                            ->maxLength(20)
                                            ->helperText('Format: 628xxxxxxxxxx (tanpa + atau spasi)'),

                                        Textarea::make('address')
                                            ->label('Alamat')
                                            ->rows(3),
                                    ]),

                                Section::make('Peta')
                                    ->schema([
                                        Textarea::make('maps_embed')
                                            ->label('Google Maps Embed')
                                            ->rows(3)
                                            ->helperText('Paste kode embed dari Google Maps'),

                                        TextInput::make('maps_link')
                                            ->label('Link Google Maps')
                                            ->url()
                                            ->helperText('Link langsung ke Google Maps'),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Media Sosial')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make('Akun Media Sosial')
                                    ->schema([
                                        TextInput::make('facebook')
                                            ->label('Facebook')
                                            ->url()
                                            ->placeholder('https://facebook.com/...'),

                                        TextInput::make('twitter')
                                            ->label('Twitter/X')
                                            ->url()
                                            ->placeholder('https://twitter.com/...'),

                                        TextInput::make('instagram')
                                            ->label('Instagram')
                                            ->url()
                                            ->placeholder('https://instagram.com/...'),

                                        TextInput::make('youtube')
                                            ->label('YouTube')
                                            ->url()
                                            ->placeholder('https://youtube.com/...'),

                                        TextInput::make('linkedin')
                                            ->label('LinkedIn')
                                            ->url()
                                            ->placeholder('https://linkedin.com/...'),

                                        TextInput::make('tiktok')
                                            ->label('TikTok')
                                            ->url()
                                            ->placeholder('https://tiktok.com/...'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make('Tema')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Warna Tema')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                ColorPicker::make('theme_color_primary')
                                                    ->label('Warna Primer'),

                                                ColorPicker::make('theme_color_secondary')
                                                    ->label('Warna Sekunder'),

                                                ColorPicker::make('theme_color_accent')
                                                    ->label('Warna Aksen'),
                                            ]),
                                    ]),

                                Section::make('Font')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('theme_font_heading')
                                                    ->label('Font Heading')
                                                    ->placeholder('Inter'),

                                                TextInput::make('theme_font_body')
                                                    ->label('Font Body')
                                                    ->placeholder('Inter'),
                                            ]),
                                    ]),

                                Section::make('Custom Code')
                                    ->schema([
                                        Textarea::make('custom_css')
                                            ->label('Custom CSS')
                                            ->rows(5)
                                            ->placeholder('/* CSS tambahan */')
                                            ->helperText('CSS yang akan ditambahkan ke halaman'),

                                        Textarea::make('custom_js')
                                            ->label('Custom JavaScript')
                                            ->rows(5)
                                            ->placeholder('// JavaScript tambahan')
                                            ->helperText('JavaScript yang akan ditambahkan ke halaman'),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Fitur')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Section::make('Toggle Fitur')
                                    ->schema([
                                        Toggle::make('show_announcement_bar')
                                            ->label('Tampilkan Bar Pengumuman')
                                            ->helperText('Ticker pengumuman di atas halaman'),

                                        Toggle::make('show_floating_whatsapp')
                                            ->label('Tampilkan Tombol WhatsApp')
                                            ->helperText('Tombol WhatsApp floating di pojok kanan bawah'),

                                        Toggle::make('show_back_to_top')
                                            ->label('Tampilkan Tombol Back to Top')
                                            ->helperText('Tombol scroll ke atas'),

                                        Toggle::make('enable_dark_mode')
                                            ->label('Aktifkan Dark Mode')
                                            ->helperText('Izinkan pengunjung beralih ke mode gelap'),
                                    ]),

                                Section::make('Pengaturan Konten')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('articles_per_page')
                                                    ->label('Artikel per Halaman')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(50)
                                                    ->default(12),

                                                TextInput::make('featured_articles_count')
                                                    ->label('Jumlah Artikel Featured')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(10)
                                                    ->default(4),

                                                TextInput::make('sidebar_articles_count')
                                                    ->label('Artikel di Sidebar')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(10)
                                                    ->default(5),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Footer')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Konten Footer')
                                    ->schema([
                                        TextInput::make('footer_text_left')
                                            ->label('Teks Footer Kiri')
                                            ->placeholder('© 2026 Universitas Bumigora. All rights reserved.')
                                            ->helperText('Biasanya untuk copyright')
                                            ->maxLength(255),
                                        TextInput::make('footer_text_right')
                                            ->label('Teks Footer Kanan')
                                            ->placeholder('Developed with ❤️ by PUSTIK UBG')
                                            ->helperText('Biasanya untuk credit developer')
                                            ->maxLength(255),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Determine unit type and id based on user role
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            $unitType = UnitType::UNIVERSITAS;
            $unitId = null;
        } else {
            $unitType = $user->unit_type;
            $unitId = $user->unit_id;
        }

        // Save each setting
        foreach ($data as $key => $value) {
            // Skip null/empty values for non-boolean fields
            $isBoolean = in_array($key, [
                'show_announcement_bar',
                'show_floating_whatsapp',
                'show_back_to_top',
                'enable_dark_mode'
            ]);

            if ($value !== null && ($value !== '' || $isBoolean)) {
                // Convert boolean to string
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                Setting::updateOrCreate(
                    [
                        'key' => $key,
                        'unit_type' => $unitType,
                        'unit_id' => $unitId,
                    ],
                    [
                        'value' => is_array($value) ? json_encode($value) : $value,
                        'type' => $this->getSettingType($key),
                    ]
                );
            }
        }

        // Clear settings cache
        app(SettingService::class)->clearAllCache($unitType, $unitId);

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }

    /**
     * Get setting type based on key
     */
    protected function getSettingType(string $key): string
    {
        $types = [
            'site_name' => 'text',
            'site_description' => 'textarea',
            'site_keywords' => 'text',
            'logo' => 'image',
            'logo_dark' => 'image',
            'favicon' => 'image',
            'og_image' => 'image',
            'email' => 'email',
            'phone' => 'text',
            'whatsapp' => 'text',
            'address' => 'textarea',
            'maps_embed' => 'textarea',
            'maps_link' => 'url',
            'facebook' => 'url',
            'twitter' => 'url',
            'instagram' => 'url',
            'youtube' => 'url',
            'linkedin' => 'url',
            'tiktok' => 'url',
            'theme_color_primary' => 'color',
            'theme_color_secondary' => 'color',
            'theme_color_accent' => 'color',
            'theme_font_heading' => 'text',
            'theme_font_body' => 'text',
            'custom_css' => 'code',
            'custom_js' => 'code',
            'show_announcement_bar' => 'boolean',
            'show_floating_whatsapp' => 'boolean',
            'show_back_to_top' => 'boolean',
            'enable_dark_mode' => 'boolean',
            'articles_per_page' => 'integer',
            'featured_articles_count' => 'integer',
            'sidebar_articles_count' => 'integer',
            'footer_text_left' => 'text',
            'footer_text_right' => 'text',
        ];

        return $types[$key] ?? 'text';
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save'),
        ];
    }
}
