<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IpLocationService
{
    /**
     * Resolve a human-friendly "City, Country" from an IP, using a free API.
     * Returns empty string on failure.
     */
    public static function resolveCityCountry(?string $ip): string
    {
        $ip = trim((string) $ip);
        if ($ip === '' || $ip === '127.0.0.1' || $ip === '::1') {
            return '';
        }

        try {
            // ipwho.is does not require an API key for basic usage
            $resp = Http::timeout(5)->get("https://ipwho.is/{$ip}");
            if (!$resp->ok()) {
                return '';
            }
            $data = $resp->json();
            if (!($data['success'] ?? false)) {
                return '';
            }
            $city = trim((string) ($data['city'] ?? ''));
            $country = trim((string) ($data['country'] ?? ''));
            $parts = array_filter([$city, $country], fn ($v) => $v !== '');
            return implode(', ', $parts);
        } catch (\Throwable $e) {
            // fail silently
            return '';
        }
    }

    /**
     * Build the persisted "ip (City, Country)" label.
     */
    public static function buildIpLocationLabel(?string $ip): string
    {
        $ip = trim((string) $ip);
        $loc = static::resolveCityCountry($ip);
        return $loc !== '' ? "{$ip} ({$loc})" : ($ip !== '' ? $ip : '');
    }
}






