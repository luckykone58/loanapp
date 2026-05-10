<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CloudflareService
{
	/** @var string */
	protected $apiToken;
	/** @var string|null */
	protected $hostingIp;

	public function __construct()
	{
		$this->apiToken = (string) env('CLOUDFLARE_API_KEY', '');
		$this->hostingIp = env('HOSTING_IP');
	}

	protected function client()
	{
		return Http::withHeaders([
			'Authorization' => 'Bearer '.$this->apiToken,
			'Content-Type' => 'application/json',
		])->baseUrl('https://api.cloudflare.com/client/v4');
	}

	protected function isReady(): bool
	{
		return $this->apiToken !== '' && !empty($this->hostingIp);
	}

	/**
	 * Find a Cloudflare zone ID for a given host by trying candidate parent domains.
	 * Example: "app.sub.example.com" will try zones: "app.sub.example.com", "sub.example.com", "example.com".
	 */
	public function findZoneIdForHost(string $host): ?string
	{
		if (!$this->isReady()) {
			return null;
		}
		$host = strtolower(trim($host));
		if ($host === '') {
			return null;
		}
		$parts = array_values(array_filter(explode('.', $host)));
		for ($i = 0; $i < count($parts); $i++) {
			$zone = implode('.', array_slice($parts, $i));
			$resp = $this->client()->get('/zones', [
				'name' => $zone,
				'status' => 'active',
				'per_page' => 1,
			]);
			if ($resp->successful() && !empty($resp->json('result'))) {
				return (string) ($resp->json('result.0.id') ?? '');
			}
		}
		return null;
	}

	/**
	 * Ensure an A record exists for the host pointing to HOSTING_IP.
	 * Creates or updates the record. Returns true on success.
	 */
	public function ensureARecord(string $host, bool $proxied = true): bool
	{
		if (!$this->isReady()) {
			return false;
		}
		$zoneId = $this->findZoneIdForHost($host);
		if (!$zoneId) {
			\Log::warning('Cloudflare: zone not found for host', ['host' => $host]);
			return false;
		}
		// Check existing record
		$list = $this->client()->get("/zones/{$zoneId}/dns_records", [
			'type' => 'A',
			'name' => $host,
			'per_page' => 1,
		]);
		if ($list->successful() && !empty($list->json('result'))) {
			$rec = $list->json('result.0');
			$recId = (string) ($rec['id'] ?? '');
			$payload = [
				'type' => 'A',
				'name' => $host,
				'content' => (string) $this->hostingIp,
				'ttl' => 1,
				'proxied' => $proxied,
			];
			$update = $this->client()->put("/zones/{$zoneId}/dns_records/{$recId}", $payload);
			return $update->successful();
		}
		// Create new record
		$payload = [
			'type' => 'A',
			'name' => $host,
			'content' => (string) $this->hostingIp,
			'ttl' => 1,
			'proxied' => $proxied,
		];
		$create = $this->client()->post("/zones/{$zoneId}/dns_records", $payload);
		return $create->successful();
	}

	/**
	 * Delete an A record for the given host (if exists). Returns true if deleted/found or false on error.
	 */
	public function deleteARecord(string $host): bool
	{
		if (!$this->isReady()) {
			return false;
		}
		$zoneId = $this->findZoneIdForHost($host);
		if (!$zoneId) {
			return false;
		}
		$list = $this->client()->get("/zones/{$zoneId}/dns_records", [
			'type' => 'A',
			'name' => $host,
			'per_page' => 100,
		]);
		if (!$list->successful()) {
			return false;
		}
		$ok = true;
		foreach ((array) $list->json('result', []) as $rec) {
			if (($rec['type'] ?? '') === 'A' && strtolower((string) ($rec['name'] ?? '')) === strtolower($host)) {
				$recId = (string) ($rec['id'] ?? '');
				if ($recId !== '') {
					$del = $this->client()->delete("/zones/{$zoneId}/dns_records/{$recId}");
					$ok = $ok && $del->successful();
				}
			}
		}
		return $ok;
	}
}


