<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Support\Settings;
use Illuminate\Support\Str;
use App\Support\ActivityLog;

class ProfileController extends Controller
{
	public function editPersonal()
	{
		$user = Auth::user();
		$info = $user->info;
		return view('profile-personal', compact('info', 'user'));
	}

	public function updatePersonal(Request $request)
	{
		$user = Auth::user();
		$info = $user->info ?: new UserInfo([
			'domain_id' => $user->domain_id,
			'user_id' => $user->id,
		]);

		$reqs = Settings::getJson('loan_requirements', []);
		$requireRel1 = !empty($reqs['relative_1']);
		$requireRel2 = !empty($reqs['relative_2']);

		$request->validate([
			'full_name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string',  'max:255'],
			'address' => ['required', 'string', 'max:1000'],
			'company' => ['required', 'string', 'max:255'],
			'company_address' => ['required', 'string', 'max:1000'],
			'position' => ['required', 'string', 'max:255'],
			'monthly_income' => ['required', 'string', 'max:255'],
			'contact_1_person' => [$requireRel1 ? 'required' : 'nullable', 'string', 'max:255'],
			'contact_1_phone' => [$requireRel1 ? 'required' : 'nullable', 'string', 'max:255'],
			'contact_1_relativity' => [$requireRel1 ? 'required' : 'nullable', 'string', 'max:255'],
			'contact_2_person' => [$requireRel2 ? 'required' : 'nullable', 'string', 'max:255'],
			'contact_2_phone' => [$requireRel2 ? 'required' : 'nullable', 'string', 'max:255'],
			'contact_2_relativity' => [$requireRel2 ? 'required' : 'nullable', 'string', 'max:255'],
		]);

		foreach ([
			'full_name','email','address','company','company_address','position','monthly_income',
			'contact_1_person','contact_1_phone','contact_1_relativity',
			'contact_2_person','contact_2_phone','contact_2_relativity',
		] as $field) {
			$info->{$field} = $request->input($field, $info->{$field});
		}
		// Initialize credit score from settings default if not already set
		if (empty($info->credit_score)) {
			$defaultCredit = (int) Settings::get('default_credit_score', 30);
			$info->credit_score = $defaultCredit;
		}
		$info->save();

		// Also update the user's display name to match full_name
		$user->name = (string) $request->input('full_name', $user->name);
		$user->save();

		try {
			ActivityLog::forUser($user, 'update info', [
				'section' => 'personal',
				'changed' => $request->only([
					'full_name','email','address','company','company_address','position','monthly_income',
					'contact_1_person','contact_1_phone','contact_1_relativity',
					'contact_2_person','contact_2_phone','contact_2_relativity',
				]),
			]);
		} catch (\Throwable $e) {}

		return back()->with('success', 'Profile updated.');
	}

	public function editId()
	{
		$user = Auth::user();
		$info = $user->info;
		return view('profile-id', compact('info', 'user'));
	}

	public function updateId(Request $request)
	{
		$user = Auth::user();
		$info = $user->info ?: new UserInfo([
			'domain_id' => $user->domain_id,
			'user_id' => $user->id,
		]);

		$request->validate([
			'id_card_number' => ['required', 'string', 'max:255'],
			'id_card_front' => ['required', 'image', 'max:5120'],
			'id_card_back' => ['required', 'image', 'max:5120'],
			'id_card_selfie' => ['required', 'image', 'max:5120'],
		]);

		$info->id_card_number = $request->input('id_card_number', $info->id_card_number);
		$dir = $this->userProfileDir($user);
        foreach (['id_card_front','id_card_back','id_card_selfie'] as $field) {
            if ($request->hasFile($field)) {
                // Store original file without resizing or format conversion
                $info->{$field} = $request->file($field)->store($dir, 'public');
            }
        }
		$info->save();

		try {
			ActivityLog::forUser($user, 'update info', [
				'section' => 'id',
				'id_card_number' => $info->id_card_number,
			]);
		} catch (\Throwable $e) {}

		return back()->with('success', 'ID information updated.');
	}

	public function editBank()
	{
		$user = Auth::user();
		$info = $user->info;
		return view('profile-bank', compact('info', 'user'));
	}

	public function updateBank(Request $request)
	{
		$user = Auth::user();
		$info = $user->info ?: new UserInfo([
			'domain_id' => $user->domain_id,
			'user_id' => $user->id,
		]);

		$request->validate([
			'bank_name' => ['nullable', 'string', 'max:255'],
			'bank_number' => ['nullable', 'string', 'max:255'],
		]);

		$info->bank_name = $request->input('bank_name', $info->bank_name);
		$info->bank_number = $request->input('bank_number', $info->bank_number);
		$info->save();

		try {
			ActivityLog::forUser($user, 'update info', [
				'section' => 'bank',
				'bank_name' => $info->bank_name,
				'bank_number' => $info->bank_number,
			]);
		} catch (\Throwable $e) {}

		return back()->with('success', 'Bank information updated.');
	}

	public function editSignature()
	{
		$user = Auth::user();
		$info = $user->info;
		return view('profile-signature', compact('info', 'user'));
	}

	public function updateSignature(Request $request)
	{
		$user = Auth::user();
		$info = $user->info ?: new UserInfo([
			'domain_id' => $user->domain_id,
			'user_id' => $user->id,
		]);

		$request->validate([
			'signature' => ['nullable', 'image', 'max:5120'],
			'signature_data' => ['nullable', 'string'], // data:image/jpeg;base64,...
		]);

		$stored = false;
		$dir = $this->userProfileDir($user);
        $signatureRelPath = ''; // will be set based on mime

		// Prefer canvas data if present
		$dataUrl = (string) $request->input('signature_data', '');
        if ($dataUrl !== '' && preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $dataUrl, $m) === 1) {
            try {
                $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
                $base64 = preg_replace('/^data:image\/(?:png|jpg|jpeg);base64,/', '', $dataUrl);
                $binary = base64_decode($base64, true);
                if ($binary === false) {
                    throw new \RuntimeException('Invalid signature data.');
                }
                $signatureRelPath = rtrim($dir, '/').'/signature.'.$ext;
                \Illuminate\Support\Facades\Storage::disk('public')->put($signatureRelPath, $binary, ['visibility' => 'public']);
                $info->signature = $signatureRelPath;
                $info->save();
                $stored = true;
            } catch (\Throwable $e) {
                return back()->withErrors(['signature' => 'Failed to save signature: '.$e->getMessage()]);
            }
        }

		// Fallback: file upload (if provided instead of canvas)
		if (!$stored && $request->hasFile('signature')) {
			$storedPath = $request->file('signature')->store($dir, 'public');
			$info->signature = $storedPath;
			$info->save();
			$stored = true;
		}

		if (!$stored) {
			return back()->withErrors(['signature' => 'Please draw your signature before submitting.']);
		}

		try {
			ActivityLog::forUser($user, 'update info', [
				'section' => 'signature',
				'signature' => $info->signature,
			]);
		} catch (\Throwable $e) {}

		return back()->with('success', 'Signature updated.');
	}

	protected function userProfileDir($user): string
	{
		$host = 'default';
		if (app()->bound('currentDomain') && app('currentDomain')?->host) {
			$host = (string) app('currentDomain')->host;
		}
		$slug = Str::slug($host);
		return trim($slug.'/profile/'.$user->id, '/');
	}

    // Removed image conversion helper. Files are stored as uploaded.
}
