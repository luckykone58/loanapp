<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Scopes\DomainScope;
use App\Support\ActivityLog;

class NotificationsController extends Controller
{
	public function index(Request $request)
	{
		$isSuper = auth('admin')->user()?->role === 'SuperAdmin';
		$query = Notification::query();
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
			$query->where(function ($q) use ($search) {
				$q->where('title', 'like', '%'.$search.'%')
				  ->orWhere('message', 'like', '%'.$search.'%')
				  ->orWhere('notes', 'like', '%'.$search.'%')
				  ->orWhereHas('user', function ($uq) use ($search) {
					  $uq->where('username', 'like', '%'.$search.'%')
						 ->orWhere('name', 'like', '%'.$search.'%');
				  });
			});
		}
		$from = $request->date('from');
		$to = $request->date('to');
		if ($from && $to) {
			$query->whereBetween('created_date', [
				\Carbon\Carbon::parse($from)->startOfDay(),
				\Carbon\Carbon::parse($to)->endOfDay(),
			]);
		} elseif ($from) {
			$query->where('created_date', '>=', \Carbon\Carbon::parse($from)->startOfDay());
		} elseif ($to) {
			$query->where('created_date', '<=', \Carbon\Carbon::parse($to)->endOfDay());
		}
		$notifications = $query->orderByDesc('id')->paginate(15)->appends($request->only('q','from','to'));
		return view('admin.notifications.index', compact('notifications', 'search', 'from', 'to'));
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
		return view('admin.notifications.create', compact('users'));
	}

	public function store(Request $request)
	{
		$request->validate([
			'user_id' => ['required', Rule::exists('users', 'id')],
			'title' => ['required', 'string', 'max:255'],
			'message' => ['required', 'string', 'max:255'],
			'subtext' => ['nullable', 'string'],
			'notes' => ['nullable', 'string'],
			'type' => ['required', Rule::in(['loan','withdrawal','account'])],
			'status' => ['required', Rule::in(['unread', 'read'])],
			'created_date' => ['nullable', 'date'],
		]);

		$n = Notification::create($request->only('user_id', 'title', 'message', 'subtext', 'notes', 'type', 'status', 'created_date'));
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				$affected = \App\Models\User::find($n->user_id);
				ActivityLog::forAdmin($admin, 'created notification', [
					'notification_id' => $n->id,
					'user_id' => $n->user_id,
					'title' => $n->title,
				], $affected);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.notifications.index')->with('success', 'Notification created.');
	}

	public function edit(Notification $notification)
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
		return view('admin.notifications.edit', compact('notification', 'users'));
	}

	public function update(Request $request, Notification $notification)
	{
		$request->validate([
			'user_id' => ['required', Rule::exists('users', 'id')],
			'title' => ['required', 'string', 'max:255'],
			'message' => ['required', 'string', 'max:255'],
			'subtext' => ['nullable', 'string'],
			'notes' => ['nullable', 'string'],
			'type' => ['required', Rule::in(['loan','withdrawal','account'])],
			'status' => ['required', Rule::in(['unread', 'read'])],
			'created_date' => ['nullable', 'date'],
		]);

		$notification->update($request->only('user_id', 'title', 'message', 'subtext', 'notes', 'type', 'status', 'created_date'));
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				$affected = \App\Models\User::find($notification->user_id);
				ActivityLog::forAdmin($admin, 'updated notification', [
					'notification_id' => $notification->id,
					'user_id' => $notification->user_id,
					'title' => $notification->title,
				], $affected);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.notifications.index')->with('success', 'Notification updated.');
	}

	public function destroy(Notification $notification)
	{
		$notification->delete();
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				$affected = \App\Models\User::find($notification->user_id);
				ActivityLog::forAdmin($admin, 'deleted notification', [
					'notification_id' => $notification->id,
					'user_id' => $notification->user_id,
					'title' => $notification->title,
				], $affected);
			}
		} catch (\Throwable $e) {}
		return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted.');
	}
}


