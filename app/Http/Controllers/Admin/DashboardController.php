<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Scopes\DomainScope;

class DashboardController extends Controller
{
	public function index()
	{
		$now = Carbon::now();

		$isSuper = Auth::guard('admin')->user()?->role === 'SuperAdmin';
		$userBase = User::query();
		$loanBase = Loan::query();
		if ($isSuper) {
			$userBase->withoutGlobalScope(DomainScope::class);
			$loanBase->withoutGlobalScope(DomainScope::class);
		}

		$periods = [
			'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
			'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
			'last_week' => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->endOfWeek()],
			'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
			'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->endOfMonth()],
		];

		$stats = [];
		foreach ($periods as $label => [$from, $to]) {
			$registered = (clone $userBase)
				->whereBetween('created_at', [$from, $to])
				->count();

			$loanAgg = (clone $loanBase)
				->whereBetween('created_date', [$from, $to])
				->selectRaw('COUNT(*) as cnt, COALESCE(SUM(amount),0) as sum_amount')
				->first();

			$stats[$label] = [
				'registered' => $registered,
				'loan_requests' => (int) ($loanAgg->cnt ?? 0),
				'loan_amount' => (float) ($loanAgg->sum_amount ?? 0),
			];
		}

		return view('admin.dashboard', compact('stats'));
	}
}


