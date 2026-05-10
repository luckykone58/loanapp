<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Setting;
use App\Scopes\DomainScope;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\ActivityLog;

class DomainsController extends Controller
{
	public function index(Request $request)
	{
		$query = Domain::query();
		// Filters
		if ($search = trim((string) $request->string('q'))) {
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', '%'.$search.'%')
				  ->orWhere('host', 'like', '%'.$search.'%');
			});
		}
		$from = $request->date('from');
		$to = $request->date('to');
		if ($from && $to) {
			$query->whereBetween('expired_date', [
				\Carbon\Carbon::parse($from)->startOfDay(),
				\Carbon\Carbon::parse($to)->endOfDay(),
			]);
		} elseif ($from) {
			$query->where('expired_date', '>=', \Carbon\Carbon::parse($from)->startOfDay());
		} elseif ($to) {
			$query->where('expired_date', '<=', \Carbon\Carbon::parse($to)->endOfDay());
		}
		$domains = $query->orderBy('id', 'desc')->paginate(15)->appends($request->only('q','from','to'));
		// Auto-suspend only when expired_date is reached or passed
		try {
			$now = \Carbon\Carbon::now()->startOfDay();
			foreach ($domains as $d) {
				if (!empty($d->expired_date)) {
					$exp = \Carbon\Carbon::parse($d->expired_date)->startOfDay();
					$days = $now->diffInDays($exp, false); // negative when expired
					if ($days <= 0 && $d->status !== 'Suspend') {
						$d->status = 'Suspend';
						$d->save();
					}
				}
			}
		} catch (\Throwable $e) {
			\Log::warning('Domain auto-suspend pass failed', ['error' => $e->getMessage()]);
		}
		return view('admin.domains.index', compact('domains', 'search', 'from', 'to'));
	}

	public function create()
	{
		return view('admin.domains.create');
	}

	public function store(Request $request)
	{
		$request->validate([
			'name' => ['nullable', 'string', 'max:255'],
			'host' => ['required', 'string', 'max:255', 'unique:domains,host'],
			'status' => ['nullable', 'in:Active,Suspend,Removed'],
			'expired_date' => ['nullable', 'date'],
		]);
		$domain = Domain::create([
			'name' => $request->input('name'),
			'host' => $request->input('host'),
			'status' => $request->input('status', 'Active'),
			'expired_date' => $request->input('expired_date'),
		]);
		// Clone settings from the base domain (ID 1) into the new domain
		try {
			$sourceId = 1;
			$settings = Setting::query()
				->withoutGlobalScope(DomainScope::class)
				->where('domain_id', $sourceId)
				->get(['name', 'value']);
			if ($settings->isNotEmpty()) {
				$rows = $settings->map(function ($s) use ($domain) {
					return [
						'domain_id' => (int) $domain->id,
						'name' => (string) $s->name,
						'value' => (string) $s->value,
					];
				})->all();
				// Bulk insert
				if (!empty($rows)) {
					Setting::query()->insert($rows);
				}
			}
		} catch (\Throwable $e) {
			\Log::warning('Failed to clone settings for new domain', [
				'new_domain_id' => $domain->id,
				'error' => $e->getMessage(),
			]);
		}
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'created domain', [
					'domain_id' => $domain->id,
					'host' => $domain->host,
				]);
			}
		} catch (\Throwable $e) {}
		return redirect()->route('admin.domains.index')->with('success', 'Domain created and settings cloned.');
	}

	public function edit(Domain $domain)
	{
		return view('admin.domains.edit', compact('domain'));
	}

	public function update(Request $request, Domain $domain)
	{
		$request->validate([
			'name' => ['nullable', 'string', 'max:255'],
			'host' => ['required', 'string', 'max:255', Rule::unique('domains', 'host')->ignore($domain->id)],
			'status' => ['nullable', 'in:Active,Suspend,Removed'],
			'expired_date' => ['nullable', 'date'],
		]);
		$oldHost = (string) $domain->host;
		$domain->update([
			'name' => $request->input('name'),
			'host' => $request->input('host'),
			'status' => $request->input('status', $domain->status ?? 'Active'),
			'expired_date' => $request->input('expired_date'),
		]);
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'updated domain', [
					'domain_id' => $domain->id,
					'host' => $domain->host,
				]);
			}
		} catch (\Throwable $e) {}
		return redirect()->route('admin.domains.index')->with('success', 'Domain updated.');
	}

	public function destroy(Domain $domain)
	{
		$host = (string) $domain->host;
		$domain->delete();
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'deleted domain', [
					'domain_id' => $domain->id,
					'host' => $domain->host,
				]);
			}
		} catch (\Throwable $e) {}
		return redirect()->route('admin.domains.index')->with('success', 'Domain deleted.');
	}
}


