<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class AppConfigService
{
    /**
     * Nilai yang menandakan unlimited
     */
    public const UNLIMITED = -1;

    protected ?array $licenseData = null;
    protected ?bool $isValid = null;
    protected ?string $error = null;

    /**
     * Check if license validation is enabled
     * Always enabled in production for security
     */
    public function isEnabled(): bool
    {
        // Always enabled in production - cannot be bypassed
        if (app()->environment('production')) {
            return true;
        }
        
        return config('system.enabled', true);
    }

    /**
     * Validate and decode the license key
     */
    public function validate(): bool
    {
        // If disabled, always valid
        if (!$this->isEnabled()) {
            return true;
        }

        // Use cached result if available
        if ($this->isValid !== null) {
            return $this->isValid;
        }

        // Check cache first (valid for 1 hour)
        $cacheKey = 'sys_cfg_' . md5(config('system.key', ''));
        $cached = Cache::get($cacheKey);
        
        if ($cached !== null) {
            $this->licenseData = $cached['data'];
            $this->isValid = $cached['valid'];
            $this->error = $cached['error'];
            return $this->isValid;
        }

        $this->isValid = $this->performValidation();
        
        // Cache result
        Cache::put($cacheKey, [
            'data' => $this->licenseData,
            'valid' => $this->isValid,
            'error' => $this->error,
        ], now()->addHour());

        return $this->isValid;
    }

    /**
     * Perform actual validation
     */
    protected function performValidation(): bool
    {
        $licenseKey = config('system.key', '');
        
        if (empty($licenseKey)) {
            $this->error = 'License key tidak ditemukan';
            return false;
        }

        // Split key into data and signature
        $parts = explode('.', $licenseKey);
        if (count($parts) !== 2) {
            $this->error = 'Format license key tidak valid';
            return false;
        }

        [$dataBase64, $signatureBase64] = $parts;

        // Decode data
        $dataJson = base64_decode(strtr($dataBase64, '-_', '+/'));
        if ($dataJson === false) {
            $this->error = 'Gagal decode license data';
            return false;
        }

        $data = json_decode($dataJson, true);
        if ($data === null) {
            $this->error = 'Gagal parse license data';
            return false;
        }

        // Verify signature
        $signature = base64_decode(strtr($signatureBase64, '-_', '+/'));
        if ($signature === false) {
            $this->error = 'Gagal decode signature';
            return false;
        }

        $publicKey = openssl_pkey_get_public(config('system.public_key'));
        if (!$publicKey) {
            $this->error = 'Public key tidak valid';
            return false;
        }

        $verified = openssl_verify($dataJson, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        if ($verified !== 1) {
            $this->error = 'Signature tidak valid';
            return false;
        }

        // Check expiration
        $expires = $data['e'] ?? null;
        if ($expires && strtotime($expires) < time()) {
            $this->error = 'License sudah expired pada ' . $expires;
            return false;
        }

        // Check domain (optional, for extra security)
        $licensedDomain = $data['d'] ?? null;
        $currentDomain = config('app.domain', request()->getHost());
        
        // Store valid license data
        $this->licenseData = [
            'max_fakultas' => $data['f'] ?? 0,
            'max_prodi' => $data['p'] ?? 0,
            'domain' => $licensedDomain,
            'expires' => $expires,
            'issued' => isset($data['i']) ? date('Y-m-d H:i:s', $data['i']) : null,
            'name' => $data['n'] ?? '',
            'version' => $data['v'] ?? 1,
        ];

        return true;
    }

    /**
     * Get license data
     */
    public function getData(): ?array
    {
        $this->validate();
        return $this->licenseData;
    }

    /**
     * Get validation error message
     */
    public function getError(): ?string
    {
        $this->validate();
        return $this->error;
    }

    /**
     * Get max allowed fakultas
     * Returns PHP_INT_MAX for unlimited (-1)
     */
    public function getMaxFakultas(): int
    {
        if (!$this->isEnabled()) {
            return PHP_INT_MAX; // Unlimited when disabled
        }
        
        $this->validate();
        $max = $this->licenseData['max_fakultas'] ?? 0;
        
        // -1 means unlimited
        return $max === self::UNLIMITED ? PHP_INT_MAX : $max;
    }

    /**
     * Check if fakultas is unlimited
     */
    public function isFakultasUnlimited(): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }
        
        $this->validate();
        return ($this->licenseData['max_fakultas'] ?? 0) === self::UNLIMITED;
    }

    /**
     * Get max allowed prodi
     * Returns PHP_INT_MAX for unlimited (-1)
     */
    public function getMaxProdi(): int
    {
        if (!$this->isEnabled()) {
            return PHP_INT_MAX; // Unlimited when disabled
        }
        
        $this->validate();
        $max = $this->licenseData['max_prodi'] ?? 0;
        
        // -1 means unlimited
        return $max === self::UNLIMITED ? PHP_INT_MAX : $max;
    }

    /**
     * Check if prodi is unlimited
     */
    public function isProdiUnlimited(): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }
        
        $this->validate();
        return ($this->licenseData['max_prodi'] ?? 0) === self::UNLIMITED;
    }

    /**
     * Check if can create more fakultas
     */
    public function canCreateFakultas(): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        if (!$this->validate()) {
            return false;
        }

        $currentCount = \App\Models\Fakultas::count();
        return $currentCount < $this->getMaxFakultas();
    }

    /**
     * Check if can create more prodi
     */
    public function canCreateProdi(): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        if (!$this->validate()) {
            return false;
        }

        $currentCount = \App\Models\Prodi::count();
        return $currentCount < $this->getMaxProdi();
    }

    /**
     * Get remaining fakultas slots
     * Returns PHP_INT_MAX if unlimited
     */
    public function getRemainingFakultas(): int
    {
        if (!$this->isEnabled() || $this->isFakultasUnlimited()) {
            return PHP_INT_MAX;
        }

        $currentCount = \App\Models\Fakultas::count();
        return max(0, $this->getMaxFakultas() - $currentCount);
    }

    /**
     * Get remaining prodi slots
     * Returns PHP_INT_MAX if unlimited
     */
    public function getRemainingProdi(): int
    {
        if (!$this->isEnabled() || $this->isProdiUnlimited()) {
            return PHP_INT_MAX;
        }

        $currentCount = \App\Models\Prodi::count();
        return max(0, $this->getMaxProdi() - $currentCount);
    }

    /**
     * Get license status summary
     */
    public function getStatus(): array
    {
        $isValid = $this->validate();
        
        $fakultasUnlimited = $this->isFakultasUnlimited();
        $prodiUnlimited = $this->isProdiUnlimited();
        
        return [
            'enabled' => $this->isEnabled(),
            'valid' => $isValid,
            'error' => $this->error,
            'data' => $this->licenseData,
            'usage' => [
                'fakultas' => [
                    'current' => \App\Models\Fakultas::count(),
                    'max' => $fakultasUnlimited ? 'Unlimited' : $this->getMaxFakultas(),
                    'remaining' => $fakultasUnlimited ? 'Unlimited' : $this->getRemainingFakultas(),
                    'unlimited' => $fakultasUnlimited,
                ],
                'prodi' => [
                    'current' => \App\Models\Prodi::count(),
                    'max' => $prodiUnlimited ? 'Unlimited' : $this->getMaxProdi(),
                    'remaining' => $prodiUnlimited ? 'Unlimited' : $this->getRemainingProdi(),
                    'unlimited' => $prodiUnlimited,
                ],
            ],
            'developer' => config('system.developer'),
        ];
    }

    /**
     * Clear license cache
     */
    public function clearCache(): void
    {
        $cacheKey = 'sys_cfg_' . md5(config('system.key', ''));
        Cache::forget($cacheKey);
        $this->isValid = null;
        $this->licenseData = null;
        $this->error = null;
    }
}
