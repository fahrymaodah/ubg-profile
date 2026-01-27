<?php

namespace App\Models;

use App\Enums\PrestasiKategori;
use App\Enums\PrestasiTingkat;
use App\Enums\UnitType;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory, HasUnit;

    protected $table = 'prestasi';

    protected $fillable = [
        'unit_type',
        'unit_id',
        'judul',
        'deskripsi',
        'tanggal',
        'tingkat',
        'kategori',
        'penyelenggara',
        'lokasi',
        'peserta',
        'pembimbing',
        'foto',
        'gallery',
        'link',
        'sertifikat',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => UnitType::class,
            'tingkat' => PrestasiTingkat::class,
            'kategori' => PrestasiKategori::class,
            'tanggal' => 'date',
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get related unit (Fakultas or Prodi)
     */
    public function getUnitAttribute()
    {
        return match ($this->unit_type) {
            UnitType::FAKULTAS => Fakultas::find($this->unit_id),
            UnitType::PRODI => Prodi::find($this->unit_id),
            default => null,
        };
    }

    /**
     * Scope for active prestasi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured prestasi
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope by tingkat
     */
    public function scopeByTingkat($query, PrestasiTingkat $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    /**
     * Scope by kategori
     */
    public function scopeByKategori($query, PrestasiKategori $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope ordered by date
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('tanggal');
    }

    /**
     * Scope by year
     */
    public function scopeByYear($query, int $year)
    {
        return $query->whereYear('tanggal', $year);
    }

    /**
     * Get prestasi for fakultas (including all prodi in fakultas)
     */
    public static function forFakultasWithProdi(int $fakultasId)
    {
        $prodiIds = Prodi::where('fakultas_id', $fakultasId)->pluck('id');
        
        return static::where(function ($query) use ($fakultasId, $prodiIds) {
            $query->where(function ($q) use ($fakultasId) {
                $q->where('unit_type', UnitType::FAKULTAS)
                  ->where('unit_id', $fakultasId);
            })->orWhere(function ($q) use ($prodiIds) {
                $q->where('unit_type', UnitType::PRODI)
                  ->whereIn('unit_id', $prodiIds);
            });
        });
    }

    /**
     * Get all prestasi for universitas (all fakultas and prodi)
     */
    public static function forUniversitas()
    {
        return static::active();
    }
}
