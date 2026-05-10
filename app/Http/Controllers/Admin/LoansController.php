<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\User;
use App\Models\Notification;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Scopes\DomainScope;
use App\Support\ActivityLog;

	class LoansController extends Controller
{
	public function index(Request $request)
	{
		$isSuper = auth('admin')->user()?->role === 'SuperAdmin';
		$query = Loan::query();
		if ($isSuper) {
			$query->withoutGlobalScope(DomainScope::class)
			      ->with(['user' => function ($uq) {
					  $uq->withoutGlobalScope(DomainScope::class)
					     ->with(['domain', 'info']);
				  }]);
		} else {
			$query->with(['user', 'user.domain', 'user.info']);
		}
		// Filters
		if ($search = trim((string) $request->string('q'))) {
			$query->where(function ($q) use ($search) {
				$q->where('loan_number', 'like', '%'.$search.'%')
				  ->orWhereHas('user', function ($uq) use ($search) {
					  $uq->where('username', 'like', '%'.$search.'%')
						 ->orWhere('name', 'like', '%'.$search.'%');
				  });
			});
		}
		$from = $request->date('from');
		$to = $request->date('to');
		if ($from && $to) {
			$query->whereBetween('start_date', [
				\Carbon\Carbon::parse($from)->startOfDay(),
				\Carbon\Carbon::parse($to)->endOfDay(),
			]);
		} elseif ($from) {
			$query->where('start_date', '>=', \Carbon\Carbon::parse($from)->startOfDay());
		} elseif ($to) {
			$query->where('start_date', '<=', \Carbon\Carbon::parse($to)->endOfDay());
		}
		$loans = $query->orderByDesc('id')->paginate(15)->appends($request->only('q','from','to'));
		return view('admin.loans.index', compact('loans', 'search', 'from', 'to'));
	}

	public function create()
	{
		$admin = auth('admin')->user();
		$isSuper = $admin?->role === 'SuperAdmin';
		$query = User::query()->where('role', 'user');
		// If admin has a domain_id, restrict users to that domain
		if ($admin?->domain_id) {
			$query->where('domain_id', $admin->domain_id);
		} elseif ($isSuper) {
			// SuperAdmin without domain_id sees all 'user' role (no domain filter)
			// leave unfiltered
		}
		$users = $query->orderBy('username')->get();
		return view('admin.loans.create', compact('users'));
	}

	public function store(Request $request)
	{
		$request->validate([
			'user_id' => ['required', Rule::exists('users', 'id')],
			'loan_number' => ['required', 'string', 'max:64', Rule::unique('loans', 'loan_number')],
			'amount' => ['required', 'numeric'],
			'start_date' => ['required', 'date'],
			'period' => ['required', 'integer', 'min:1'],
			'interest' => ['required', 'numeric'],
			'status' => ['required', Rule::in(['processing', 'approved', 'rejected', 'paid'])],
		]);

		$loan = Loan::create($request->only('user_id', 'loan_number', 'amount', 'start_date', 'period', 'interest', 'status'));
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				$affected = \App\Models\User::find($loan->user_id);
				ActivityLog::forAdmin($admin, 'created loan', [
					'loan_id' => $loan->id,
					'user_id' => $loan->user_id,
					'amount' => $loan->amount,
				], $affected);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.loans.index')->with('success', 'Loan created.');
	}

	public function edit(Loan $loan)
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
		return view('admin.loans.edit', compact('loan', 'users'));
	}

	public function update(Request $request, Loan $loan)
	{
		$request->validate([
			'user_id' => ['required', Rule::exists('users', 'id')],
			'loan_number' => ['required', 'string', 'max:64', Rule::unique('loans', 'loan_number')->ignore($loan->id)],
			'amount' => ['required', 'numeric'],
			'start_date' => ['required', 'date'],
			'period' => ['required', 'integer', 'min:1'],
			'interest' => ['required', 'numeric'],
			'status' => ['required', Rule::in(['processing', 'approved', 'rejected', 'paid'])],
		]);

		$loan->update($request->only('user_id', 'loan_number', 'amount', 'start_date', 'period', 'interest', 'status'));
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				$affected = \App\Models\User::find($loan->user_id);
				ActivityLog::forAdmin($admin, 'updated loan', [
					'loan_id' => $loan->id,
					'user_id' => $loan->user_id,
					'amount' => $loan->amount,
				], $affected);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.loans.index')->with('success', 'Loan updated.');
	}

	public function destroy(Loan $loan)
	{
		$loan->delete();
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				$affected = \App\Models\User::find($loan->user_id);
				ActivityLog::forAdmin($admin, 'deleted loan', [
					'loan_id' => $loan->id,
					'user_id' => $loan->user_id,
					'amount' => $loan->amount,
				], $affected);
			}
		} catch (\Throwable $e) {}
		return redirect()->route('admin.loans.index')->with('success', 'Loan deleted.');
	}

	/**
	 * Handle "Review" quick action from loans index.
	 * Creates a notification and optionally updates loan status based on selected title.
	 */
	public function review(Request $request, Loan $loan)
	{
		$validated = $request->validate([
			'title' => ['required', 'string', 'max:190'],
			'message' => ['required', 'string'],
			'subtext' => ['nullable', 'string', 'max:500'],
			'notes' => ['nullable', 'string', 'max:1000'],
		]);

		$title = $validated['title'];
		$status = strtolower($title);
		if (in_array($status, ['approved', 'rejected'], true)) {
			// Only act on transition to avoid duplicate side-effects
			$wasStatus = strtolower((string) $loan->status);
			if ($status !== $wasStatus) {
				$loan->status = $status;
				$loan->save();
				// If approved, add loan amount to user's wallet in users_info
				if ($status === 'approved') {
					$info = $loan->user?->info;
					if ($info) {
						$info->wallet = (float) ($info->wallet ?? 0) + (float) $loan->amount;
						$info->save();
					}
				}
				try {
					$admin = auth('admin')->user();
					if ($admin) {
						\App\Support\ActivityLog::forAdmin($admin, $status, [
							'loan_id' => $loan->id,
							'user_id' => $loan->user_id,
							'amount' => $loan->amount,
						], $loan->user);
					}
				} catch (\Throwable $e) {}
			}
		}

		Notification::create([
			'domain_id'    => $loan->domain_id,
			'user_id'      => $loan->user_id,
			'title'        => $title,
			'message'      => $validated['message'],
			'subtext'      => $validated['subtext'] ?? null,
			'notes'        => $validated['notes'] ?? null,
			'type'         => 'loan',
			'created_date' => now(),
			'status'       => 'unread',
		]);

		return redirect()->route('admin.loans.index')->with('success', 'Review saved.');
	}
}


