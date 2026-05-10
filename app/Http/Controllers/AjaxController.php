<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AjaxController extends Controller
{
	/**
	 * Return the authenticated user's notifications (latest first).
	 */
	public function notifications(Request $request): JsonResponse
	{
		$user = Auth::user();
		if (!$user) {
			return response()->json(['error' => 'Unauthorized'], 401);
		}

		$items = Notification::query()
			->where('user_id', $user->id)
			->orderByDesc('id')
			->limit((int) $request->integer('limit', 50))
			->get();

		$mapped = $items->map(function (Notification $n) {
			return [
				'id' => (int) $n->id,
				'type' => (string) ($n->type ?? 'account'),
				'title' => (string) ($n->title ?? ''),
				'message' => (string) ($n->message ?? ''),
				'subtext' => (string) ($n->subtext ?? ''),
				'notes' => (string) ($n->notes ?? ''),
				'status' => (string) $n->status,
				'isRead' => (string) $n->status === 'read',
				'created_at' => $n->created_date ? Carbon::parse($n->created_date)->toIso8601String() : null,
				'created_human' => $n->created_date ? Carbon::parse($n->created_date)->diffForHumans() : null,
			];
		});

		return response()->json(['data' => $mapped]);
	}

	/**
	 * Unread notifications count for the authenticated user.
	 */
	public function unreadCount(): JsonResponse
	{
		$user = Auth::user();
		if (!$user) {
			return response()->json(['error' => 'Unauthorized'], 401);
		}

		$count = Notification::query()
			->where('user_id', $user->id)
			->where('status', 'unread')
			->count();

		return response()->json(['count' => $count]);
	}

	/**
	 * Mark a single notification as read.
	 */
	public function markRead(Request $request, int $id): JsonResponse
	{
		$user = Auth::user();
		if (!$user) {
			return response()->json(['error' => 'Unauthorized'], 401);
		}

		$affected = Notification::query()
			->where('id', $id)
			->where('user_id', $user->id)
			->update(['status' => 'read']);

		return response()->json(['ok' => $affected > 0]);
	}

	/**
	 * Mark all notifications as read.
	 */
	public function markAllRead(): JsonResponse
	{
		$user = Auth::user();
		if (!$user) {
			return response()->json(['error' => 'Unauthorized'], 401);
		}

		Notification::query()
			->where('user_id', $user->id)
			->where('status', 'unread')
			->update(['status' => 'read']);

		return response()->json(['ok' => true]);
	}
}








