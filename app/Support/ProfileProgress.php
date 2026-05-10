<?php

namespace App\Support;

use App\Models\User;

class ProfileProgress
{
	/**
	 * Compute profile completion status for a user based on settings requirements.
	 * Returns an array with keys:
	 * - hasPersonal, needId, hasId, needBank, hasBank, needSignature, hasSignature (bool)
	 * - stepsOrder (array of step keys), completedMap (step=>bool)
	 * - currentKey (string|null), done (int), total (int), percent (int), completed (bool)
	 */
	public static function getStatusForUser(User $user): array
	{
		$reqs = Settings::getJson('loan_requirements', []);
		$info = $user->info;

		$hasPersonal = !empty($info?->full_name);
		$needId = (bool)($reqs['id_number'] ?? false) || (bool)($reqs['id_front'] ?? false) || (bool)($reqs['id_back'] ?? false) || (bool)($reqs['id_selfie'] ?? false);
		$needBank = (bool)($reqs['bank_name'] ?? false) || (bool)($reqs['bank_account'] ?? false);
		$needSignature = (bool)($reqs['signature'] ?? false);

		$hasId = !empty($info?->id_card_number) || !empty($info?->id_card_front) || !empty($info?->id_card_back) || !empty($info?->id_card_selfie);
		$hasBank = !empty($info?->bank_name) || !empty($info?->bank_number);
		$hasSignature = !empty($info?->signature);

		$stepsOrder = ['personal'];
		if ($needId) $stepsOrder[] = 'id';
		if ($needBank) $stepsOrder[] = 'bank';
		if ($needSignature) $stepsOrder[] = 'signature';

		$completedMap = [
			'personal' => $hasPersonal,
			'id' => $hasId,
			'bank' => $hasBank,
			'signature' => $hasSignature,
		];

		$currentKey = null;
		foreach ($stepsOrder as $k) {
			if (!($completedMap[$k] ?? false)) { $currentKey = $k; break; }
		}

		$total = count($stepsOrder);
		$done = 0; foreach ($stepsOrder as $k) { if ($completedMap[$k] ?? false) $done++; }
		$percent = (int) round(($total > 0 ? ($done / $total) : 0) * 100);

		return [
			'hasPersonal' => $hasPersonal,
			'needId' => $needId,
			'hasId' => $hasId,
			'needBank' => $needBank,
			'hasBank' => $hasBank,
			'needSignature' => $needSignature,
			'hasSignature' => $hasSignature,
			'stepsOrder' => $stepsOrder,
			'completedMap' => $completedMap,
			'currentKey' => $currentKey,
			'done' => $done,
			'total' => $total,
			'percent' => $percent,
			'completed' => $done >= $total && $total > 0,
		];
	}

	/**
	 * Shortcut: whether the user has completed all required steps.
	 */
	public static function isComplete(User $user): bool
	{
		$status = self::getStatusForUser($user);
		return (bool) ($status['completed'] ?? false);
	}
}






