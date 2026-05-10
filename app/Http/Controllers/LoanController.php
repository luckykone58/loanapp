<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Loan;
use App\Models\Notification;
use App\Support\Settings;
use Carbon\Carbon;
use App\Support\ActivityLog;

class LoanController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Settings and constraints
        $min = (float) Settings::get('loan_min', 0);
        $max = (float) Settings::get('loan_max', 1000000);
        $terms = Settings::getJson('loan_terms', [3, 6, 12, 24, 36, 48, 60]);
        $allowedPeriods = array_values(array_map('intval', is_array($terms) ? $terms : []));
        if (empty($allowedPeriods)) {
            $allowedPeriods = [3, 6, 12, 24, 36, 48, 60];
        }
        $interestPercent = (float) Settings::get('loan_interest_rate', 0.5); // store percent value

        $request->validate([
            'amount' => ['required', 'numeric', 'min:'.$min, 'max:'.$max],
            'period' => ['required', 'integer', 'in:'.implode(',', $allowedPeriods)],
        ]);

        // Generate loan number: YYYYMMDDHHMMSS
        $loanNumber = Carbon::now()->format('YmdHis');

        // Persist loan
        $loan = Loan::create([
            'user_id' => $user->id,
            'loan_number' => $loanNumber,
            'amount' => (float) $request->amount,
            'start_date' => Carbon::now()->toDateString(),
            'period' => (int) $request->period,
            'interest' => $interestPercent,
            'status' => 'processing',
        ]);

        try {
            ActivityLog::forUser($user, 'request loan', [
                'loan_id' => $loan->id,
                'loan_number' => $loan->loan_number,
                'amount' => $loan->amount,
                'period' => $loan->period,
                'interest' => $loan->interest,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        // Create notification
        try {
            Notification::create([
                'user_id' => $user->id,
                'title' => (string) __('loan.notifications.submitted.title'),
                'message' => (string) __('loan.notifications.submitted.message'),
                'subtext' => '',
                'notes' => (string) __('loan.notifications.submitted.notes'),
                'type' => 'loan',
                'status' => 'unread',
                'created_date' => Carbon::now()->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Loan notification failed', ['loan_id' => $loan->id ?? null, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'message' => __('loan.notifications.submitted.message'),
            'loan_id' => $loan->id,
            'loan_number' => $loan->loan_number,
            'status' => $loan->status,
        ]);
    }
}
