<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Domain;
use Illuminate\Http\Request;
use App\Scopes\DomainScope;

class LogsController extends Controller
{
	public function index(Request $request)
	{
		$admin = auth('admin')->user();
		$isSuper = $admin?->role === 'SuperAdmin';

		$query = Log::query();

		if ($isSuper) {
			$query->withoutGlobalScope(DomainScope::class)
			      ->with(['user' => function ($uq) {
				      $uq->withoutGlobalScope(DomainScope::class)
				         ->with(['domain']);
			      }, 'domain' => function ($dq) {
				      $dq->withoutGlobalScope(DomainScope::class);
			      }]);
		} else {
			$query->with(['user.domain']);
		}

		// Filters
		$search = trim((string) $request->string('q'));
		if ($search !== '') {
			$query->where(function ($q) use ($search) {
				$q->where('subject', 'like', '%'.$search.'%')
				  ->orWhereHas('user', function ($uq) use ($search) {
					  $uq->where('username', 'like', '%'.$search.'%')
					     ->orWhere('name', 'like', '%'.$search.'%');
				  });
			});
		}

		if ($isSuper && ($domainId = (int) $request->integer('domain_id'))) {
			$query->where('domain_id', $domainId);
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

		$logs = $query->orderByDesc('id')->paginate(15)->appends($request->only('q','from','to','domain_id'));
		$domains = [];
		if ($isSuper) {
			$domains = Domain::orderBy('name')->get();
		}

		return view('admin.logs.index', compact('logs', 'search', 'from', 'to', 'domains'));
	}

	public function destroy(Request $request, $id)
	{
		$admin = auth('admin')->user();
		if (!$admin || $admin->role !== 'SuperAdmin') {
			abort(403);
		}
		$log = \App\Models\Log::withoutGlobalScope(DomainScope::class)->findOrFail((int) $id);
		$log->delete();
		return redirect()->route('admin.logs.index')->with('success', 'Log deleted.');
	}
}


