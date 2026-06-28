<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahApiService
{
    protected string $baseUrl;
    protected string $provinceId;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = config('services.wilayah_api.base_url', 'https://www.emsifa.com/api-wilayah-indonesia/api');
        $this->provinceId = config('services.wilayah_api.province_id', '35');
        $this->cacheTtl = config('services.wilayah_api.cache_ttl', 86400);
    }

    public function getKabupatens(): array
    {
        return $this->rememberFallback('wilayah:kabupatens', function () {
            return $this->fetch("{$this->baseUrl}/regencies/{$this->provinceId}.json");
        });
    }

    public function getKecamatans(string $kabupatenId): array
    {
        return $this->rememberFallback("wilayah:kecamatans:{$kabupatenId}", function () use ($kabupatenId) {
            return $this->fetch("{$this->baseUrl}/districts/{$kabupatenId}.json");
        });
    }

    public function getDesas(string $kecamatanId): array
    {
        return $this->rememberFallback("wilayah:desas:{$kecamatanId}", function () use ($kecamatanId) {
            return $this->fetch("{$this->baseUrl}/villages/{$kecamatanId}.json");
        });
    }

    /**
     * Cache data with fallback: if fetch fails, use stale cache
     */
    protected function rememberFallback(string $key, callable $callback): array
    {
        $data = Cache::get($key);

        if ($data !== null) {
            return $data;
        }

        $fresh = $callback();

        if (!empty($fresh)) {
            Cache::put($key, $fresh, $this->cacheTtl);
            return $fresh;
        }

        Log::warning("WilayahApiService: failed to fetch data for key [{$key}], returning empty.");

        return [];
    }

    protected function fetch(string $url): array
    {
        try {
            $response = Http::timeout(15)->retry(3, 1000)->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("WilayahApiService: HTTP {$response->status()} from {$url}");
        } catch (\Exception $e) {
            Log::error("WilayahApiService: connection error to {$url} — {$e->getMessage()}");
        }

        return [];
    }
}
