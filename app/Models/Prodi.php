<?php

namespace App\Models;

use App\Enums\Jenjang;
use App\Enums\UnitType;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'prodi';

    protected $fillable = [
        'fakultas_id',
        'nama',
        'slug',
        'subdomain',
        'kode',
        'jenjang',
        'deskripsi',
        'logo',
        'banner',
        'visi',
        'misi',
        'tujuan',
        'sejarah',
        'struktur_organisasi',
        'struktur_image',
        'profil_lulusan',
        'kompetensi',
        'akreditasi',
        'no_sk_akreditasi',
        'tanggal_akreditasi',
        'kurikulum_file',
        'alamat',
        'telepon',
        'email',
        'website',
        'social_media',
        'is_active',
        'is_published',
        'published_at',
        'coming_soon_message',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'jenjang' => Jenjang::class,
            'social_media' => 'array',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'tanggal_akreditasi' => 'date',
        ];
    }

    /**
     * Field used for slug generation
     */
    protected string $slugSource = 'nama';

    /**
     * Get the fakultas that owns this prodi
     */
    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    /**
     * Get all dosen in this prodi
     */
    public function dosen(): HasMany
    {
        return $this->hasMany(Dosen::class)->orderBy('order');
    }

    /**
     * Get all menus for this prodi
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'unit_id')
            ->where('unit_type', UnitType::PRODI);
    }

    /**
     * Get all articles for this prodi
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'unit_id')
            ->where('unit_type', UnitType::PRODI);
    }

    /**
     * Get all pages for this prodi
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'unit_id')
            ->where('unit_type', UnitType::PRODI);
    }

    /**
     * Get all prestasi for this prodi
     */
    public function prestasi(): HasMany
    {
        return $this->hasMany(Prestasi::class, 'unit_id')
            ->where('unit_type', UnitType::PRODI);
    }

    /**
     * Get all sliders for this prodi
     */
    public function sliders(): HasMany
    {
        return $this->hasMany(Slider::class, 'unit_id')
            ->where('unit_type', UnitType::PRODI);
    }

    /**
     * Get all users managing this prodi
     */
    public function admins(): HasMany
    {
        return $this->hasMany(User::class, 'unit_id')
            ->where('unit_type', UnitType::PRODI);
    }

    /**
     * Get full URL for this prodi
     */
    public function getUrlAttribute(): string
    {
        return 'https://' . $this->subdomain . '.' . config('app.domain');
    }

    /**
     * Get full name with jenjang
     */
    public function getFullNameAttribute(): string
    {
        return $this->jenjang->shortLabel() . ' ' . $this->nama;
    }

    /**
     * Scope for active prodi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for published prodi
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('nama');
    }

    /**
     * Scope by fakultas
     */
    public function scopeByFakultas($query, int $fakultasId)
    {
        return $query->where('fakultas_id', $fakultasId);
    }

    /**
     * Scope by jenjang
     */
    public function scopeByJenjang($query, Jenjang $jenjang)
    {
        return $query->where('jenjang', $jenjang);
    }

    /**
     * Find by subdomain
     */
    public static function findBySubdomain(string $subdomain): ?self
    {
        return static::where('subdomain', $subdomain)->first();
    }
}
