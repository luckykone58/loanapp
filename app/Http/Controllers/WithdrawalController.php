<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Withdrawal;
use App\Support\ActivityLog;

class WithdrawalController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $info = $user->info;
        $wallet = (float) ($info?->wallet ?? 0);
        $expectedCode = (string) ($info?->withdrawal_code ?? '');

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'withdrawal_code' => ['required', 'string'],
        ]);

        // Prevent multiple in-progress withdrawals
        $hasPending = Withdrawal::where('user_id', $user->id)->where('status', 'processing')->exists();
        if ($hasPending) {
            return back()->with('error', __('wallets.error.invalid_withdrawal_code'));
        }

        $amount = (float) $data['amount'];
        $code = (string) $data['withdrawal_code'];

        if ($amount > $wallet) {
            return back()->with('error', __('wallets.error.amount_exceeds_wallet'));
        }
        if (empty($expectedCode) || $code !== $expectedCode) {
            return back()->with('error', __('wallets.error.invalid_withdrawal_code'));
        }

        Withdrawal::create([
            'domain_id' => $user->domain_id,
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'processing',
            'withdraw_name' => (string) ($info?->bank_name ?? ''),
            'withdraw_number' => (string) ($info?->bank_number ?? ''),
        ]);

        try {
            ActivityLog::forUser($user, 'request withdrawal', [
                'amount' => $amount,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success', __('wallets.message.withdrawal_submitted'));
    }
}
