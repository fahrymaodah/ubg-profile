<?php

namespace App\Filament\Pages;

use App\Enums\UnitType;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProfilUnit extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected string $view = 'filament.pages.profil-unit';

    protected static string | \UnitEnum | null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil Unit';

    protected static ?string $title = 'Profil Unit';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public ?string $unitTypeValue = null;
    public ?int $unitId = null;

    protected function getUnitType(): ?UnitType
    {
        return $this->unitTypeValue ? UnitType::from($this->unitTypeValue) : null;
    }

    protected function getUnit()
    {
        $unitType = $this->getUnitType();
        
        if ($unitType === UnitType::FAKULTAS && $this->unitId) {
            return Fakultas::find($this->unitId);
        }
        
        if ($unitType === UnitType::PRODI && $this->unitId) {
            return Prodi::find($this->unitId);
        }
        
        return null;
    }

    public function mount(): void
    {
        $user = auth()->user();

        // Determine unit type and id based on user role
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            $this->unitTypeValue = UnitType::UNIVERSITAS->value;
            $this->unitId = null;
        } elseif ($user->isFakultas()) {
            $this->unitTypeValue = UnitType::FAKULTAS->value;
            $this->unitId = $user->unit_id;
        } else {
            $this->unitTypeValue = UnitType::PRODI->value;
            $this->unitId = $user->unit_id;
        }

        // Load existing data
        $this->form->fill($this->loadData());
    }

    /**
     * Convert stored string/JSON to repeater array format
     */
    protected function parseRepeaterData(?string $data): array
    {
        if (empty($data)) {
            return [];
        }

        // Try to decode as JSON first
        $decoded = json_decode($data, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Fallback: parse as line-separated text
        $lines = array_filter(array_map('trim', explode("\n", strip_tags($data))));
        return array_map(fn($line) => ['item' => $line], $lines);
    }

    /**
     * Convert repeater array to JSON string for storage
     */
    protected function formatRepeaterData(?array $data): string
    {
        if (empty($data)) {
            return '[]';
        }

        return json_encode(array_values($data));
    }

    protected function loadData(): array
    {
        $unitType = $this->getUnitType();
        
        if ($unitType === UnitType::UNIVERSITAS) {
            // Load from settings
            $settings = Setting::query()
                ->where('unit_type', UnitType::UNIVERSITAS)
                ->whereNull('unit_id')
                ->whereIn('key', [
                    'profil_visi',
                    'profil_misi',
                    'profil_tujuan',
                    'profil_sejarah',
                    'profil_struktur_organisasi',
                    'profil_struktur_image',
                ])
                ->pluck('value', 'key')
                ->toArray();

            // Parse sejarah JSON
            $sejarah = json_decode($settings['profil_sejarah'] ?? '{}', true) ?: [];
            
            // Parse struktur JSON
            $struktur = json_decode($settings['profil_struktur_organisasi'] ?? '{}', true) ?: [];
            
            // FileUpload expects array format for existing files
            $sejarahImage = $sejarah['image'] ?? null;
            $strukturImage = $settings['profil_struktur_image'] ?? null;
            
            // Parse pejabat images to array format
            $pejabat = $struktur['pejabat'] ?? [];
            foreach ($pejabat as &$p) {
                if (!empty($p['image']) && !is_array($p['image'])) {
                    $p['image'] = [$p['image']];
                }
            }

            return [
                'visi' => $settings['profil_visi'] ?? '',
                'misi' => $this->parseRepeaterData($settings['profil_misi'] ?? ''),
                'tujuan' => $this->parseRepeaterData($settings['profil_tujuan'] ?? ''),
                'sejarah_pendirian' => $sejarah['pendirian'] ?? '',
                'sejarah_lokasi' => $sejarah['lokasi'] ?? '',
                'sejarah_akreditasi' => $sejarah['akreditasi'] ?? '',
                'sejarah_image' => $sejarahImage ? [$sejarahImage] : [],
                'sejarah_image_caption' => $sejarah['image_caption'] ?? '',
                'sejarah_timeline' => $sejarah['timeline'] ?? [],
                'struktur_image' => $strukturImage ? [$strukturImage] : [],
                'struktur_pejabat' => $pejabat,
                'struktur_pendukung' => $struktur['pendukung'] ?? [],
            ];
        }

        // Load from unit model (Fakultas/Prodi)
        $unit = $this->getUnit();
        if ($unit) {
            // Parse sejarah JSON
            $sejarah = json_decode($unit->sejarah ?? '{}', true) ?: [];
            
            // Parse struktur JSON
            $struktur = json_decode($unit->struktur_organisasi ?? '{}', true) ?: [];
            
            // FileUpload expects array format for existing files
            $sejarahImage = $sejarah['image'] ?? null;
            $strukturImage = $unit->struktur_image ?? null;
            
            // Parse pejabat images to array format
            $pejabat = $struktur['pejabat'] ?? [];
            foreach ($pejabat as &$p) {
                if (!empty($p['image']) && !is_array($p['image'])) {
                    $p['image'] = [$p['image']];
                }
            }

            return [
                'visi' => $unit->visi ?? '',
                'misi' => $this->parseRepeaterData($unit->misi ?? ''),
                'tujuan' => $this->parseRepeaterData($unit->tujuan ?? ''),
                'sejarah_pendirian' => $sejarah['pendirian'] ?? '',
                'sejarah_lokasi' => $sejarah['lokasi'] ?? '',
                'sejarah_akreditasi' => $sejarah['akreditasi'] ?? '',
                'sejarah_image' => $sejarahImage ? [$sejarahImage] : [],
                'sejarah_image_caption' => $sejarah['image_caption'] ?? '',
                'sejarah_timeline' => $sejarah['timeline'] ?? [],
                'struktur_image' => $strukturImage ? [$strukturImage] : [],
                'struktur_pejabat' => $pejabat,
                'struktur_pendukung' => $struktur['pendukung'] ?? [],
            ];
        }

        return [];
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Profil')
                    ->tabs([
                        Tab::make('Visi & Misi')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Section::make('Visi')
                                    ->description('Visi adalah gambaran masa depan yang ingin dicapai oleh unit.')
                                    ->schema([
                                        Textarea::make('visi')
                                            ->label('Visi')
                                            ->rows(4)
                                            ->placeholder('Masukkan visi unit...'),
                                    ]),

                                Section::make('Misi')
                                    ->description('Misi adalah langkah-langkah untuk mencapai visi.')
                                    ->schema([
                                        Repeater::make('misi')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('item')
                                                    ->label('')
                                                    ->placeholder('Masukkan poin misi...')
                                                    ->required(),
                                            ])
                                            ->addActionLabel('+ Tambah Misi')
                                            ->reorderable(false)
                                            ->cloneable(false)
                                            ->collapsible(false)
                                            ->defaultItems(1)
                                            ->itemLabel(null)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Tujuan')
                                    ->description('Tujuan adalah sasaran yang ingin dicapai. (Opsional)')
                                    ->schema([
                                        Repeater::make('tujuan')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('item')
                                                    ->label('')
                                                    ->placeholder('Masukkan poin tujuan...')
                                                    ->required(),
                                            ])
                                            ->addActionLabel('+ Tambah Tujuan')
                                            ->reorderable(false)
                                            ->cloneable(false)
                                            ->collapsible(false)
                                            ->defaultItems(0)
                                            ->itemLabel(null)
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
                            ]),

                        Tab::make('Sejarah')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('Fakta Singkat')
                                    ->description('Informasi dasar tentang unit.')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('sejarah_pendirian')
                                            ->label('Tahun Pendirian')
                                            ->placeholder('1995'),
                                        TextInput::make('sejarah_lokasi')
                                            ->label('Lokasi')
                                            ->placeholder('Mataram, NTB'),
                                        TextInput::make('sejarah_akreditasi')
                                            ->label('Akreditasi')
                                            ->placeholder('Baik Sekali (B)'),
                                    ]),

                                Section::make('Gambar Sejarah')
                                    ->description('Gambar yang ditampilkan di sidebar halaman sejarah.')
                                    ->columns(2)
                                    ->schema([
                                        FileUpload::make('sejarah_image')
                                            ->label('Gambar')
                                            ->image()
                                            ->imageEditor()
                                            ->disk('public')
                                            ->directory('profil')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->helperText('Upload gambar kampus/gedung (Max 5MB)'),
                                        TextInput::make('sejarah_image_caption')
                                            ->label('Keterangan Gambar')
                                            ->placeholder('Kampus Universitas Bumigora'),
                                    ]),

                                Section::make('Timeline Sejarah')
                                    ->description('Tambahkan poin-poin penting dalam perjalanan unit.')
                                    ->schema([
                                        Repeater::make('sejarah_timeline')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('tahun')
                                                    ->label('Tahun')
                                                    ->placeholder('Tahun')
                                                    ->extraInputAttributes(['class' => 'self-center']),
                                                TextInput::make('judul')
                                                    ->label('Judul')
                                                    ->placeholder('Judul')
                                                    ->required()
                                                    ->extraInputAttributes(['class' => 'self-center']),
                                                Textarea::make('deskripsi')
                                                    ->label('Deskripsi')
                                                    ->placeholder('Deskripsi')
                                                    ->rows(1),
                                            ])
                                            ->columns([
                                                'default' => 1,
                                                'sm' => 5,
                                            ])
                                            ->extraAttributes(['class' => 'timeline-repeater'])
                                            ->addActionLabel('+ Tambah Timeline')
                                            ->reorderable()
                                            ->cloneable(false)
                                            ->collapsible(false)
                                            ->defaultItems(1)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Struktur Organisasi')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make('Gambar Struktur (Opsional)')
                                    ->description('Upload gambar bagan struktur jika sudah ada dalam bentuk gambar.')
                                    ->collapsed()
                                    ->collapsible()
                                    ->schema([
                                        FileUpload::make('struktur_image')
                                            ->label('Gambar Struktur Organisasi')
                                            ->image()
                                            ->imageEditor()
                                            ->disk('public')
                                            ->directory('profil')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->helperText('Upload gambar bagan struktur organisasi (Max 5MB). Jika diisi, chart dinamis tidak ditampilkan.'),
                                    ]),

                                Section::make('Struktur Pejabat')
                                    ->description('Atur posisi pejabat dalam grid. Row 1 = paling atas (misal Rektor), Row 2 = di bawahnya (misal Wakil Rektor), dst.')
                                    ->schema([
                                        Repeater::make('struktur_pejabat')
                                            ->label('')
                                            ->schema([
                                                Grid::make(6)->schema([
                                                    TextInput::make('jabatan')
                                                        ->label('Jabatan')
                                                        ->placeholder('Rektor')
                                                        ->required()
                                                        ->columnSpan(2),
                                                    TextInput::make('nama')
                                                        ->label('Nama')
                                                        ->placeholder('Prof. Dr. H. Nama')
                                                        ->columnSpan(2),
                                                    Select::make('size')
                                                        ->label('Ukuran Card')
                                                        ->options([
                                                            'xl' => 'XL - Sangat Besar (dengan foto)',
                                                            'lg' => 'LG - Besar (dengan foto)',
                                                            'md' => 'MD - Sedang (dengan foto)',
                                                            'sm' => 'SM - Kecil (tanpa foto)',
                                                            'xs' => 'XS - Sangat Kecil (tanpa foto)',
                                                        ])
                                                        ->default('md')
                                                        ->columnSpan(2),
                                                ]),
                                                Grid::make(6)->schema([
                                                    FileUpload::make('image')
                                                        ->label('Foto Pejabat')
                                                        ->image()
                                                        ->disk('public')
                                                        ->directory('profil/pejabat')
                                                        ->visibility('public')
                                                        ->maxSize(2048)
                                                        ->helperText('Opsional. Tampil di ukuran XL, LG, MD')
                                                        ->columnSpan(2),
                                                    TextInput::make('row')
                                                        ->label('Baris (Row)')
                                                        ->numeric()
                                                        ->default(1)
                                                        ->minValue(1)
                                                        ->maxValue(10)
                                                        ->helperText('1 = paling atas')
                                                        ->columnSpan(2),
                                                    TextInput::make('column')
                                                        ->label('Kolom (Column)')
                                                        ->numeric()
                                                        ->default(1)
                                                        ->minValue(1)
                                                        ->maxValue(12)
                                                        ->helperText('1 = paling kiri')
                                                        ->columnSpan(2),
                                                ]),
                                            ])
                                            ->addActionLabel('+ Tambah Pejabat')
                                            ->reorderable()
                                            ->cloneable()
                                            ->defaultItems(0)
                                            ->columnSpanFull()
                                            ->itemLabel(fn (array $state): ?string => 
                                                ($state['jabatan'] ?? 'Pejabat') . ' - Row ' . ($state['row'] ?? '?') . ', Col ' . ($state['column'] ?? '?')
                                            ),
                                    ]),

                                Section::make('Unit Pendukung')
                                    ->description('Biro, UPT, Lembaga pendukung dengan icon dan warna kustom')
                                    ->collapsed()
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('struktur_pendukung')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('nama')
                                                    ->label('Nama Unit')
                                                    ->placeholder('UPT Perpustakaan')
                                                    ->required(),
                                                Select::make('icon')
                                                    ->label('Icon')
                                                    ->options([
                                                        'academic-cap' => '🎓 Academic Cap',
                                                        'book-open' => '📖 Book Open',
                                                        'building-library' => '🏛️ Library',
                                                        'building-office' => '🏢 Office',
                                                        'calculator' => '🧮 Calculator',
                                                        'chart-bar' => '📊 Chart',
                                                        'clipboard-document' => '📋 Clipboard',
                                                        'cog' => '⚙️ Cog/Settings',
                                                        'computer-desktop' => '🖥️ Computer',
                                                        'currency-dollar' => '💵 Currency',
                                                        'document-text' => '📄 Document',
                                                        'globe-alt' => '🌐 Globe',
                                                        'home' => '🏠 Home',
                                                        'identification' => '🪪 ID Card',
                                                        'light-bulb' => '💡 Light Bulb',
                                                        'megaphone' => '📣 Megaphone',
                                                        'newspaper' => '📰 Newspaper',
                                                        'presentation-chart-bar' => '📈 Presentation',
                                                        'server' => '🖥️ Server',
                                                        'shield-check' => '🛡️ Shield',
                                                        'user-group' => '👥 User Group',
                                                        'users' => '👤 Users',
                                                        'wrench-screwdriver' => '🔧 Tools',
                                                    ])
                                                    ->default('building-office')
                                                    ->searchable(),
                                                ColorPicker::make('color')
                                                    ->label('Warna Icon')
                                                    ->default('#6366F1'),
                                            ])
                                            ->columns(3)
                                            ->addActionLabel('+ Tambah Unit Pendukung')
                                            ->reorderable()
                                            ->defaultItems(0)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $unitType = $this->getUnitType();

        // Handle FileUpload array format - get first item or null
        $sejarahImage = $data['sejarah_image'] ?? [];
        $sejarahImagePath = is_array($sejarahImage) ? ($sejarahImage[0] ?? null) : $sejarahImage;
        
        $strukturImage = $data['struktur_image'] ?? [];
        $strukturImagePath = is_array($strukturImage) ? ($strukturImage[0] ?? null) : $strukturImage;

        // Format sejarah as JSON
        $sejarahJson = json_encode([
            'pendirian' => $data['sejarah_pendirian'] ?? '',
            'lokasi' => $data['sejarah_lokasi'] ?? '',
            'akreditasi' => $data['sejarah_akreditasi'] ?? '',
            'image' => $sejarahImagePath,
            'image_caption' => $data['sejarah_image_caption'] ?? '',
            'timeline' => array_values($data['sejarah_timeline'] ?? []),
        ]);

        // Format struktur as JSON - process pejabat images
        $pejabat = array_values($data['struktur_pejabat'] ?? []);
        foreach ($pejabat as &$p) {
            // Handle FileUpload array format
            if (isset($p['image']) && is_array($p['image'])) {
                $p['image'] = $p['image'][0] ?? null;
            }
            // Ensure row and column are integers
            $p['row'] = (int) ($p['row'] ?? 1);
            $p['column'] = (int) ($p['column'] ?? 1);
        }
        
        $strukturJson = json_encode([
            'pejabat' => $pejabat,
            'pendukung' => array_values($data['struktur_pendukung'] ?? []),
        ]);

        if ($unitType === UnitType::UNIVERSITAS) {
            // Save to settings
            $settingsToSave = [
                'profil_visi' => $data['visi'] ?? '',
                'profil_misi' => $this->formatRepeaterData($data['misi'] ?? []),
                'profil_tujuan' => $this->formatRepeaterData($data['tujuan'] ?? []),
                'profil_sejarah' => $sejarahJson,
                'profil_struktur_organisasi' => $strukturJson,
                'profil_struktur_image' => $strukturImagePath ?? '',
            ];

            foreach ($settingsToSave as $key => $value) {
                $type = 'text';
                if ($key === 'profil_struktur_image') {
                    $type = 'image';
                } elseif (in_array($key, ['profil_misi', 'profil_tujuan', 'profil_sejarah', 'profil_struktur_organisasi'])) {
                    $type = 'json';
                }

                Setting::updateOrCreate(
                    [
                        'unit_type' => UnitType::UNIVERSITAS,
                        'unit_id' => null,
                        'key' => $key,
                    ],
                    [
                        'value' => $value ?? '',
                        'type' => $type,
                    ]
                );
            }
        } else {
            // Save to unit model (Fakultas/Prodi)
            $unit = $this->getUnit();
            if ($unit) {
                $unit->update([
                    'visi' => $data['visi'] ?? null,
                    'misi' => $this->formatRepeaterData($data['misi'] ?? []),
                    'tujuan' => $this->formatRepeaterData($data['tujuan'] ?? []),
                    'sejarah' => $sejarahJson,
                    'struktur_organisasi' => $strukturJson,
                    'struktur_image' => $strukturImagePath,
                ]);
            }
        }

        Notification::make()
            ->title('Profil berhasil disimpan')
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        $user = auth()->user();

        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            return 'Profil Universitas';
        }

        if ($user->isFakultas()) {
            $fakultas = Fakultas::find($user->unit_id);
            return 'Profil ' . ($fakultas->nama ?? 'Fakultas');
        }

        $prodi = Prodi::find($user->unit_id);
        return 'Profil ' . ($prodi->nama ?? 'Program Studi');
    }

    public static function getNavigationLabel(): string
    {
        return 'Profil Unit';
    }
}
