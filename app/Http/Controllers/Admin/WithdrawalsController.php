<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Scopes\DomainScope;
use Carbon\Carbon;
use App\Support\ActivityLog;

class WithdrawalsController extends Controller
{
	public function index(Request $request)
	{
		$isSuper = auth('admin')->user()?->role === 'SuperAdmin';
		$query = Withdrawal::query();
		if ($isSuper) {
			$query->withoutGlobalScope(DomainScope::class)
			      ->with(['user' => function ($uq) {
					  $uq->withoutGlobalScope(DomainScope::class)
					     ->with(['domain']);
				  }]);
		} else {
			$query->with(['user.domain']);
		}
		// Filters
		if ($search = trim((string) $request->string('q'))) {
			$query->whereHas('user', function ($uq) use ($search) {
				$uq->where('username', 'like', '%'.$search.'%')
				   ->orWhere('name', 'like', '%'.$search.'%');
			});
		}
		$from = $request->date('from');
		$to = $request->date('to');
		if ($from && $to) {
			$query->whereBetween('created_at', [
				\Carbon\Carbon::parse($from)->startOfDay(),
				\Carbon\Carbon::parse($to)->endOfDay(),
			]);
		} elseif ($from) {
			$query->where('created_at', '>=', \Carbon\Carbon::parse($from)->startOfDay());
		} elseif ($to) {
			$query->where('created_at', '<=', \Carbon\Carbon::parse($to)->endOfDay());
		}
		$withdrawals = $query->orderByDesc('id')->paginate(15)->appends($request->only('q','from','to'));
		return view('admin.withdrawals.index', compact('withdrawals', 'search', 'from', 'to'));
	}

	public function create()
	{
		$admin = auth('admin')->user();
		$isSuper = $admin?->role === 'SuperAdmin';
		$query = User::query()->where('role', 'user');
		if ($admin?->domain_id) {
			$query->where('domain_id', $admin->domain_id);
		} elseif ($isSuper) {
			// no domain filter
		}
		$users = $query->orderBy('username')->get();
		return view('admin.withdrawals.create', compact('users'));
	}

	public function store(Request $request)
	{
		$request->validate([
			'user_id' => ['required', Rule::exists('users', 'id')],
			'amount' => ['required', 'numeric', 'min:0'],
			'status' => ['required', Rule::in(['processing', 'fulfilled', 'rejected'])],
			'withdraw_name' => ['nullable', 'string'],
			'withdraw_number' => ['nullable', 'string'],
		]);

		$w = Withdrawal::create($request->only('user_id', 'amount', 'status', 'withdraw_name', 'withdraw_number'));
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				$affected = \App\Models\User::find($w->user_id);
				ActivityLog::forAdmin($admin, 'created withdrawal', [
					'withdrawal_id' => $w->id,
					'user_id' => $w->user_id,
					'amount' => $w->amount,
				], $affected);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal created.');
	}

	public function edit(Withdrawal $withdrawal)
	{
		$admin = auth('admin')->user();
		$isSuper = $admin?->role === 'SuperAdmin';
		$query = User::query()->where('role', 'user');
		if ($admin?->domain_id) {
			$query->where('domain_id', $admin->domain_id);
		} elseif ($isSuper) {
			// no domain filter
		}
		$users = $query->orderBy('username')->get();
		return view('admin.withdrawals.edit', compact('withdrawal', 'users'));
	}

	public function update(Request $request, Withdrawal $withdrawal)
	{
		$request->validate([
			'user_id' => ['required', Rule::exists('users', 'id')],
			'amount' => ['required', 'numeric', 'min:0'],
			'status' => ['required', Rule::in(['processing', 'fulfilled', 'rejected'])],
			'withdraw_name' => ['nullable', 'string'],
			'withdraw_number' => ['nullable', 'string'],
		]);

		$withdrawal->update($request->only('user_id', 'amount', 'status', 'withdraw_name', 'withdraw_number'));
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				$affected = \App\Models\User::find($withdrawal->user_id);
				ActivityLog::forAdmin($admin, 'updated withdrawal', [
					'withdrawal_id' => $withdrawal->id,
					'user_id' => $withdrawal->user_id,
					'amount' => $withdrawal->amount,
				], $affected);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal updated.');
	}

	public function destroy(Withdrawal $withdrawal)
	{
		$withdrawal->delete();
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				$affected = \App\Models\User::find($withdrawal->user_id);
				ActivityLog::forAdmin($admin, 'deleted withdrawal', [
					'withdrawal_id' => $withdrawal->id,
					'user_id' => $withdrawal->user_id,
					'amount' => $withdrawal->amount,
				], $affected);
			}
		} catch (\Throwable $e) {}
		return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal deleted.');
	}

	/**
	 * Quick review action for approve/reject with optional notification.
	 */
	public function review(Request $request, Withdrawal $withdrawal)
	{
		$data = $request->validate([
			'status' => ['required', Rule::in(['approved', 'rejected'])],
			'title_suggestion' => ['nullable', 'string', 'max:255'],
			'title' => ['nullable', 'string', 'max:255'],
			'message' => ['required', 'string'],
			'subtext' => ['nullable', 'string', 'max:255'],
			'notes' => ['nullable', 'string'],
		]);

		$prevStatus = (string) $withdrawal->status;
		$newStatus = (string) $data['status'];
		$withdrawal->status = $newStatus;
		$withdrawal->save();

		// On approval: deduct the amount from user's wallet (once)
		if ($newStatus === 'approved' && $prevStatus !== 'approved') {
			$withdrawal->loadMissing('user.info');
			$info = $withdrawal->user?->info;
			if ($info) {
				$current = (float) ($info->wallet ?? 0);
				$amount = (float) ($withdrawal->amount ?? 0);
				$next = max(0.0, $current - $amount);
				$info->wallet = $next;
				$info->save();
			}
		}

		try {
			$admin = auth('admin')->user();
			if ($admin) {
				\App\Support\ActivityLog::forAdmin($admin, $newStatus, [
					'withdrawal_id' => $withdrawal->id,
					'user_id' => $withdrawal->user_id,
					'amount' => $withdrawal->amount,
				], $withdrawal->user);
			}
		} catch (\Throwable $e) {}

		// Create a user notification
		$title = trim((string)($data['title'] ?? '')) ?: (string)($data['title_suggestion'] ?? '');
		Notification::create([
			'domain_id' => $withdrawal->domain_id,
			'user_id' => $withdrawal->user_id,
			'title' => $title,
			'message' => (string) $data['message'],
			'subtext' => (string) ($data['subtext'] ?? ''),
			'notes' => (string) ($data['notes'] ?? ''),
			'type' => 'withdrawal',
			'status' => 'unread',
			'created_date' => Carbon::now()->toDateTimeString(),
		]);

		return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal updated and notification sent.');
	}
}



