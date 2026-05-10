<?php

namespace App\Support;

use App\Models\Log;
use App\Models\User;

class ActivityLog
{
	/**
	 * Record a log entry for a given subject & payload.
	 */
	public static function record(string $subject, array $payload = [], ?int $domainId = null, ?int $userId = null): void
	{
		try {
			$domain = $domainId;
			if ($domain === null) {
				if (app()->bound('currentDomain') && app('currentDomain')) {
					$domain = (int) app('currentDomain')->id;
				}
			}
			Log::create([
				'domain_id' => $domain,
				'user_id' => $userId,
				'subject' => $subject,
				'raw_html' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
			]);
		} catch (\Throwable $e) {
			// Never break primary flow on log failure
			\Log::warning('ActivityLog failed', [
				'subject' => $subject,
				'error' => $e->getMessage(),
			]);
		}
	}

	/**
	 * Record an action performed by a User.
	 */
	public static function forUser(User $user, string $action, array $payload = []): void
	{
		$subject = 'User '.(string) ($user->name ?? $user->username ?? 'N/A').' '.$action;
		$payload = array_merge([
			'actor' => 'user',
			'user_id' => $user->id,
			'username' => $user->username ?? null,
			'name' => $user->name ?? null,
		], $payload);
		$domainId = $user->domain_id ?? null;
		self::record($subject, $payload, $domainId, $user->id);
	}

	/**
	 * Record an action performed by an Admin (User with admin/SuperAdmin role).
	 * Optionally include an affected user for context.
	 */
	public static function forAdmin(User $admin, string $action, array $payload = [], ?User $affectedUser = null): void
	{
		$subject = 'Admin '.(string) ($admin->name ?? $admin->username ?? 'N/A').' '.$action;
		$payload = array_merge([
			'actor' => 'admin',
			'admin_id' => $admin->id,
			'admin_username' => $admin->username ?? null,
			'admin_name' => $admin->name ?? null,
		], $payload);
		if ($affectedUser) {
			$payload['affected_user_id'] = $affectedUser->id;
			$payload['affected_username'] = $affectedUser->username ?? null;
			$payload['affected_name'] = $affectedUser->name ?? null;
		}
		$domainId = $admin->domain_id ?? ($affectedUser?->domain_id ?? null);
		self::record($subject, $payload, $domainId, $affectedUser?->id);
	}
}


