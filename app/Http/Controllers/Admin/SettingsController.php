<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Support\Settings;
use App\Support\ActivityLog;

class SettingsController extends Controller
{
	public function index()
	{
        
		// Render consolidated settings panel
		$settings = $this->settingsMap([
			'theme',
			'logo_path',
			'default_locale',
			'currency_symbol',
			'show_logo_login',
			'show_logo_signup',
			'home_sliders',
			'loan_min',
			'loan_max',
			'loan_interest_rate',
			'loan_terms',
			'loan_requirements',
			'default_credit_score',
			'page_contact_visible',
			'page_about_visible',
			'page_contract_visible',
			'contact_details',
			'about_us',
			'contract_page',
			'agreement_page',
			'faqs_json',
			'welcome_title',
			'welcome_message',
			'welcome_sub_message',
		]);
		$availableThemes = $this->discoverThemes();
		$availableLocales = $this->discoverLocales();
		return view('admin.settings.panel', compact('settings', 'availableThemes', 'availableLocales'));
	}

	public function create()
	{
		return view('admin.settings.create');
	}

	public function store(Request $request)
	{
		$request->validate([
			'name' => [
				'required', 'string', 'max:255',
				Rule::unique('settings', 'name')->where(function ($q) {
					return $q->where('domain_id', app()->bound('currentDomain') ? app('currentDomain')->id : null);
				}),
			],
			'value' => ['nullable', 'string'],
		]);

		$s = Setting::create($request->only('name', 'value'));
		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'created setting', [
					'setting_id' => $s->id,
					'name' => $s->name,
				]);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.settings.index')->with('success', 'Setting created.');
	}

	public function edit(Setting $setting)
	{
		return view('admin.settings.edit', compact('setting'));
	}

	public function update(Request $request, Setting $setting)
	{
		$request->validate([
			'name' => [
				'required', 'string', 'max:255',
				Rule::unique('settings', 'name')->ignore($setting->id)->where(function ($q) use ($setting) {
					return $q->where('domain_id', $setting->domain_id);
				}),
			],
			'value' => ['nullable', 'string'],
		]);

		$setting->update($request->only('name', 'value'));
		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'updated setting', [
					'setting_id' => $setting->id,
					'name' => $setting->name,
				]);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.settings.index')->with('success', 'Setting updated.');
	}

	public function destroy(Setting $setting)
	{
		$setting->delete();
		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'deleted setting', [
					'setting_id' => $setting->id,
					'name' => $setting->name,
				]);
			}
		} catch (\Throwable $e) {}
		return redirect()->route('admin.settings.index')->with('success', 'Setting deleted.');
	}

	// --- Panel helpers and endpoints ---
	protected function settingsMap(array $keys): array
	{
		$values = Setting::query()->whereIn('name', $keys)->pluck('value', 'name')->toArray();
		return array_merge(array_fill_keys($keys, null), $values);
	}

	protected function upsert(string $name, $value): void
	{
		// Ensure domain-scoped upsert
		$domainId = null;
		if (app()->bound('currentDomain')) {
			$domainId = (int) app('currentDomain')->id;
		} elseif (\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
			$domainId = (int) \Illuminate\Support\Facades\Auth::guard('admin')->user()->domain_id;
		} elseif (\Illuminate\Support\Facades\Auth::check()) {
			$domainId = (int) \Illuminate\Support\Facades\Auth::user()->domain_id;
		}
		Setting::updateOrCreate(
			['name' => $name, 'domain_id' => $domainId],
			['value' => is_array($value) ? json_encode($value) : (string) $value]
		);
	}

	protected function discoverThemes(): array
	{
		$themesDir = base_path('templates');
		if (!is_dir($themesDir)) {
			return ['default'];
		}
		$dirs = array_values(array_filter(scandir($themesDir), function ($d) use ($themesDir) {
			return $d !== '.' && $d !== '..' && is_dir($themesDir.DIRECTORY_SEPARATOR.$d);
		}));
		return count($dirs) ? $dirs : ['default'];
	}

	protected function discoverLocales(): array
	{
		$locDir = base_path('languages');
		if (!is_dir($locDir)) {
            // Fallback to config locale/fallback
            $base = array_filter([config('app.locale'), config('app.fallback_locale')]);
            return array_values(array_unique($base ?: ['en']));
        }
		$dirs = array_values(array_filter(scandir($locDir), function ($d) use ($locDir) {
			return $d !== '.' && $d !== '..' && is_dir($locDir.DIRECTORY_SEPARATOR.$d);
		}));
		return count($dirs) ? $dirs : ['en'];
	}

	public function saveLayout(Request $request)
	{
		$request->validate([
			'theme' => ['required', 'string'],
			'default_locale' => ['required', 'string'],
			'show_logo_login' => ['nullable', 'boolean'],
			'show_logo_signup' => ['nullable', 'boolean'],
			'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
		]);
		// Sanitize theme against available templates; fallback to 'default' if invalid
		$requestedTheme = (string) $request->string('theme');
		$availableThemes = $this->discoverThemes();
		$finalTheme = in_array($requestedTheme, $availableThemes, true) ? $requestedTheme : 'default';
		$this->upsert('theme', $finalTheme);
		$this->upsert('default_locale', $request->string('default_locale'));
		$this->upsert('show_logo_login', $request->boolean('show_logo_login') ? '1' : '0');
		$this->upsert('show_logo_signup', $request->boolean('show_logo_signup') ? '1' : '0');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            if (!$file->isValid()) {
                return back()->withErrors(['logo' => 'Invalid upload. Please choose another image.']);
            }
            $mime = $file->getMimeType();
            if (strpos((string) $mime, 'image/') !== 0) {
                return back()->withErrors(['logo' => 'Only image files are allowed.']);
            }
            // Store original file without resizing or format conversion
            $diskName = 'public';
            $disk = Storage::disk($diskName);
            $dir = $this->domainFolder();
            try {
                if (method_exists($disk, 'exists') && !$disk->exists($dir)) {
                    $disk->makeDirectory($dir);
                }
            } catch (\Throwable $e) {
                \Log::warning('Logo directory ensure failed', ['disk' => $diskName, 'dir' => $dir, 'error' => $e->getMessage()]);
            }
            $relative = $file->store($dir, $diskName);
            $this->upsert('logo_path', (string) $relative);
        }

		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'updated settings layout', [
					'theme' => $finalTheme,
				]);
			}
		} catch (\Throwable $e) {}
		return back()->with('success', 'Layout settings saved.');
	}

	protected function domainFolder(): string
	{
		$host = 'default';
		$domainId = null;

		if (app()->bound('currentDomain')) {
			$domainId = (int) app('currentDomain')->id;
		} elseif (Auth::guard('admin')->check()) {
			$domainId = (int) Auth::guard('admin')->user()->domain_id;
		} elseif (Auth::check()) {
			$domainId = (int) Auth::user()->domain_id;
		}

		if ($domainId) {
			$domain = Domain::find($domainId);
			if ($domain && $domain->host) {
				$host = (string) $domain->host;
			}
		}
		// Slugify host to safe folder name
		return Str::slug($host);
	}

	public function saveLoan(Request $request)
	{
		$request->validate([
			'loan_min' => ['required', 'numeric', 'min:0'],
			'loan_max' => ['required', 'numeric', 'gte:loan_min'],
			'loan_interest_rate' => ['required', 'numeric', 'min:0'],
			'loan_terms' => ['nullable', 'array'],
			'loan_terms.*' => ['integer'],
			'currency_symbol' => ['nullable', 'string', 'max:8'],
		]);
		$this->upsert('loan_min', (string)$request->input('loan_min'));
		$this->upsert('loan_max', (string)$request->input('loan_max'));
		$this->upsert('loan_interest_rate', (string)$request->input('loan_interest_rate'));
		$this->upsert('loan_terms', $request->input('loan_terms', []));
		if ($request->has('currency_symbol')) {
			$this->upsert('currency_symbol', (string) $request->input('currency_symbol', '$'));
		}
		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'updated settings loan', [
					'loan_min' => $request->input('loan_min'),
					'loan_max' => $request->input('loan_max'),
					'loan_interest_rate' => $request->input('loan_interest_rate'),
				]);
			}
		} catch (\Throwable $e) {}
		return back()->with('success', 'Loan configuration saved.');
	}

	public function saveRequirements(Request $request)
	{
		$fields = [
			'full_name','id_number','id_front','id_back','id_selfie','signature',
			'bank_name','bank_account','relative_1','relative_2'
		];
		$reqs = [];
		foreach ($fields as $f) {
			$reqs[$f] = (bool)$request->boolean($f);
		}
		$request->validate([
			'default_credit_score' => ['nullable', 'integer', 'min:0', 'max:100000'],
		]);
		$this->upsert('loan_requirements', $reqs);
		// Persist default credit score (fallback 30)
		$this->upsert('default_credit_score', (string) $request->input('default_credit_score', 30));
		Settings::clear();
		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'updated settings requirements', $reqs);
			}
		} catch (\Throwable $e) {}
		return back()->with('success', 'Requirements saved.');
	}

	public function savePages(Request $request)
	{
		$request->validate([
			'about_us' => ['nullable', 'string'],
			'contact_details' => ['nullable', 'string'],
			'page_contact_visible' => ['nullable', 'boolean'],
			'page_about_visible' => ['nullable', 'boolean'],
			'page_contract_visible' => ['nullable', 'boolean'],
			'contract_page' => ['nullable', 'string'],
			'agreement_page' => ['nullable', 'string'],
		]);
		$this->upsert('contact_details', $request->input('contact_details', ''));
		$this->upsert('page_contact_visible', $request->boolean('page_contact_visible') ? '1' : '0');
		$this->upsert('about_us', $request->input('about_us', ''));
		$this->upsert('page_about_visible', $request->boolean('page_about_visible') ? '1' : '0');
		$this->upsert('contract_page', $request->input('contract_page', ''));
		$this->upsert('page_contract_visible', $request->boolean('page_contract_visible') ? '1' : '0');
		$this->upsert('agreement_page', $request->input('agreement_page', ''));
		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'updated settings pages', [
					'page_contact_visible' => $request->boolean('page_contact_visible'),
					'page_about_visible' => $request->boolean('page_about_visible'),
					'page_contract_visible' => $request->boolean('page_contract_visible'),
				]);
			}
		} catch (\Throwable $e) {}
		return back()->with('success', 'Pages saved.');
	}

	public function saveFaqs(Request $request)
	{
		$request->validate([
			'faqs' => ['nullable', 'array', 'max:10'],
			'faqs.*.q' => ['required', 'string', 'max:255'],
			'faqs.*.a' => ['required', 'string'],
		]);
		$this->upsert('faqs_json', array_values($request->input('faqs', [])));
		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'updated faqs', [
					'count' => count((array) $request->input('faqs', [])),
				]);
			}
		} catch (\Throwable $e) {}
		return back()->with('success', 'FAQs saved.');
	}

	public function saveWelcome(Request $request)
	{
		$request->validate([
			'welcome_title' => ['nullable', 'string', 'max:255'],
			'welcome_message' => ['nullable', 'string', 'max:1000'],
			'welcome_sub_message' => ['nullable', 'string', 'max:1000'],
		]);
		$this->upsert('welcome_title', $request->input('welcome_title', ''));
		$this->upsert('welcome_message', $request->input('welcome_message', ''));
		$this->upsert('welcome_sub_message', $request->input('welcome_sub_message', ''));
		return back()->with('success', 'Welcome Message saved.');
	}

	// --- Home Sliders management (local public storage) ---
	public function uploadSlider(Request $request)
	{
		$request->validate([
			'slides' => ['required', 'array', 'max:10'],
			'slides.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'], // 10MB per image
		]);
		$diskName = 'public';
		$disk = Storage::disk($diskName);
		$dir = $this->domainFolder().'/slides';
		// Ensure local directory exists (for local public disk)
		try {
			if (method_exists($disk, 'exists') && !$disk->exists($dir)) {
				$disk->makeDirectory($dir);
			}
		} catch (\Throwable $e) {
			\Log::warning('Failed to ensure slides directory', ['disk' => $diskName, 'dir' => $dir, 'error' => $e->getMessage()]);
		}

		$current = $this->settingsMap(['home_sliders'])['home_sliders'];
		$list = is_array($current) ? $current : (json_decode($current ?? '[]', true) ?: []);

		$uploaded = 0;
		$errors = [];
        foreach ($request->file('slides', []) as $idx => $file) {
            if (!$file->isValid()) {
                $errors["slides.$idx"] = $this->uploadErrorMessage($file->getError());
                continue;
            }
            try {
                // Store original file without resizing or format conversion
                $path = $file->store($dir, $diskName);
                $list[] = $path;
                $uploaded++;
            } catch (\Throwable $e) {
                $errors["slides.$idx"] = 'Failed to store file: '.$e->getMessage().' | debug: '.json_encode([
                    'disk' => $diskName,
                    'dir' => $dir,
                    'path' => isset($path) ? $path : null,
                    'original' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }



		$this->upsert('home_sliders', $list);
		if (!empty($errors)) {
			return back()
				->withErrors($errors)
				->with('success', $uploaded > 0 ? "$uploaded file(s) uploaded, some failed." : null);
		}
		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'uploaded sliders', [
					'uploaded' => $uploaded,
				]);
			}
		} catch (\Throwable $e) {}
		return back()->with('success', $uploaded.' file(s) uploaded.');
	}

	protected function uploadErrorMessage(int $code): string
	{
		return match ($code) {
			\UPLOAD_ERR_INI_SIZE => 'File exceeds php.ini upload_max_filesize.',
			\UPLOAD_ERR_FORM_SIZE => 'File exceeds form max file size.',
			\UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
			\UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
			\UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on server.',
			\UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
			\UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
			default => 'Unknown upload error.',
		};
	}

	public function deleteSlider(Request $request)
	{
		$request->validate([
			'path' => ['required', 'string'],
		]);
		$path = (string) $request->string('path');
		// Normalize: handle cases where settings stored full URL vs relative path
		$relative = $path;
		if (str_starts_with($relative, 'http://') || str_starts_with($relative, 'https://')) {
			$origin = rtrim(url('/'), '/');
			if (str_starts_with($relative, $origin)) {
				$relative = ltrim(substr($relative, strlen($origin)), '/');
			}
			if (str_starts_with($relative, 'storage/')) {
				$relative = substr($relative, strlen('storage/'));
			}
		}
		$publicUrl = asset('storage/'.ltrim($relative, '/'));
		$current = $this->settingsMap(['home_sliders'])['home_sliders'];
		$list = is_array($current) ? $current : (json_decode($current ?? '[]', true) ?: []);
		$list = array_values(array_filter($list, function ($p) use ($path, $relative, $publicUrl) {
			return $p !== $path && $p !== $relative && $p !== $publicUrl;
		}));

		try {
			Storage::disk('public')->delete($relative);
		} catch (\Throwable $e) {
			\Log::warning('Failed to delete slider file', ['path' => $path, 'error' => $e->getMessage()]);
		}

		$this->upsert('home_sliders', $list);
		try {
			$admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'deleted slider', [
					'path' => $relative,
				]);
			}
		} catch (\Throwable $e) {}
		return back()->with('success', 'Slide removed.');
	}
}


