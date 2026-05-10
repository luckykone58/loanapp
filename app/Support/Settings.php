<?php

namespace App\Support;

use App\Models\Setting;
use App\Scopes\DomainScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Settings
{
	/**
	 * Resolve the active domain id for the current request/user/admin.
	 */
	protected static function resolveDomainId(): ?int
	{
		if (app()->bound('currentDomain')) {
			return (int) app('currentDomain')->id;
		}
		if (Auth::guard('admin')->check()) {
			return (int) Auth::guard('admin')->user()->domain_id;
		}
		if (Auth::check()) {
			return (int) Auth::user()->domain_id;
		}
		return null;
	}

	protected static function cacheKey(?int $domainId): string
	{
		return 'settings.map.'.($domainId ?? 'null');
	}

	/**
	 * Return a map of all settings for a domain.
	 */
	public static function getMap(?int $domainId = null): array
	{
		$domainId = $domainId ?? self::resolveDomainId();
		return Cache::remember(self::cacheKey($domainId), 120, function () use ($domainId) {
			$query = Setting::query()->withoutGlobalScope(DomainScope::class);
			if (!is_null($domainId)) {
				$query->where('domain_id', $domainId);
			}
			$rows = $query->get(['name', 'value']);
			$map = [];
			foreach ($rows as $row) {
				$map[(string) $row->name] = (string) $row->value;
			}
			return $map;
		});
	}

	/**
	 * Get a single setting value.
	 */
	public static function get(string $name, $default = null, ?int $domainId = null)
	{
		$map = self::getMap($domainId);
		return array_key_exists($name, $map) ? $map[$name] : $default;
	}

	/**
	 * Get a JSON setting value decoded into an array.
	 */
	public static function getJson(string $name, $default = [], ?int $domainId = null): array
	{
		$value = self::get($name, null, $domainId);
		if (is_array($value)) {
			return $value;
		}
		$decoded = json_decode($value ?? '', true);
		return is_array($decoded) ? $decoded : (is_array($default) ? $default : []);
	}

	/**
	 * Clear cached settings for a domain.
	 */
	public static function clear(?int $domainId = null): void
	{
		$domainId = $domainId ?? self::resolveDomainId();
		Cache::forget(self::cacheKey($domainId));
	}
}








