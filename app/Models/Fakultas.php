<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\HasUnit;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Fakultas extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'fakultas';

    protected $fillable = [
        'nama',
        'slug',
        'subdomain',
        'kode',
        'deskripsi',
        'logo',
        'banner',
        'visi',
        'misi',
        'tujuan',
        'sejarah',
        'struktur_organisasi',
        'struktur_image',
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
            'social_media' => 'array',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Field used for slug generation
     */
    protected string $slugSource = 'nama';

    /**
     * Get all prodi in this fakultas
     */
    public function prodi(): HasMany
    {
        return $this->hasMany(Prodi::class)->orderBy('order');
    }

    /**
     * Get all dosen through prodi
     */
    public function dosen(): HasManyThrough
    {
        return $this->hasManyThrough(Dosen::class, Prodi::class);
    }

    /**
     * Get all menus for this fakultas
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'unit_id')
            ->where('unit_type', UnitType::FAKULTAS);
    }

    /**
     * Get all articles for this fakultas
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'unit_id')
            ->where('unit_type', UnitType::FAKULTAS);
    }

    /**
     * Get all pages for this fakultas
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'unit_id')
            ->where('unit_type', UnitType::FAKULTAS);
    }

    /**
     * Get all prestasi for this fakultas
     */
    public function prestasi(): HasMany
    {
        return $this->hasMany(Prestasi::class, 'unit_id')
            ->where('unit_type', UnitType::FAKULTAS);
    }

    /**
     * Get all sliders for this fakultas
     */
    public function sliders(): HasMany
    {
        return $this->hasMany(Slider::class, 'unit_id')
            ->where('unit_type', UnitType::FAKULTAS);
    }

    /**
     * Get all users managing this fakultas
     */
    public function admins(): HasMany
    {
        return $this->hasMany(User::class, 'unit_id')
            ->where('unit_type', UnitType::FAKULTAS);
    }

    /**
     * Get full URL for this fakultas
     */
    public function getUrlAttribute(): string
    {
        return 'https://' . $this->subdomain . '.' . config('app.domain');
    }

    /**
     * Scope for active fakultas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for published fakultas
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
     * Find by subdomain
     */
    public static function findBySubdomain(string $subdomain): ?self
    {
        return static::where('subdomain', $subdomain)->first();
    }
}
