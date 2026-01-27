<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = [
        'prodi_id',
        'nidn',
        'nip',
        'nama',
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'email',
        'telepon',
        'foto',
        'jabatan_fungsional',
        'jabatan_struktural',
        'golongan',
        'bidang_keahlian',
        'pendidikan',
        'penelitian',
        'pengabdian',
        'publikasi',
        'sinta_id',
        'google_scholar_id',
        'scopus_id',
        'orcid',
        'bio',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'pendidikan' => 'array',
            'penelitian' => 'array',
            'pengabdian' => 'array',
            'publikasi' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the prodi
     */
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Get full name with titles
     */
    public function getFullNameAttribute(): string
    {
        $name = '';
        
        if ($this->gelar_depan) {
            $name .= $this->gelar_depan . ' ';
        }
        
        $name .= $this->nama;
        
        if ($this->gelar_belakang) {
            $name .= ', ' . $this->gelar_belakang;
        }
        
        return $name;
    }

    /**
     * Alias for full_name (for backward compatibility)
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->full_name;
    }

    /**
     * Get fakultas through prodi
     */
    public function getFakultasAttribute(): ?Fakultas
    {
        return $this->prodi?->fakultas;
    }

    /**
     * Get SINTA URL
     */
    public function getSintaUrlAttribute(): ?string
    {
        return $this->sinta_id 
            ? "https://sinta.kemdikbud.go.id/authors/profile/{$this->sinta_id}"
            : null;
    }

    /**
     * Get Google Scholar URL
     */
    public function getGoogleScholarUrlAttribute(): ?string
    {
        return $this->google_scholar_id 
            ? "https://scholar.google.com/citations?user={$this->google_scholar_id}"
            : null;
    }

    /**
     * Get Scopus URL
     */
    public function getScopusUrlAttribute(): ?string
    {
        return $this->scopus_id 
            ? "https://www.scopus.com/authid/detail.uri?authorId={$this->scopus_id}"
            : null;
    }

    /**
     * Get ORCID URL
     */
    public function getOrcidUrlAttribute(): ?string
    {
        return $this->orcid 
            ? "https://orcid.org/{$this->orcid}"
            : null;
    }

    /**
     * Scope for active dosen
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('nama');
    }

    /**
     * Scope by prodi
     */
    public function scopeByProdi($query, int $prodiId)
    {
        return $query->where('prodi_id', $prodiId);
    }

    /**
     * Scope by fakultas (through prodi)
     */
    public function scopeByFakultas($query, int $fakultasId)
    {
        return $query->whereHas('prodi', function ($q) use ($fakultasId) {
            $q->where('fakultas_id', $fakultasId);
        });
    }

    /**
     * Scope by jabatan fungsional
     */
    public function scopeByJabatan($query, string $jabatan)
    {
        return $query->where('jabatan_fungsional', $jabatan);
    }
}
