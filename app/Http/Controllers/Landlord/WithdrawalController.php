<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    protected float $feePercent = 0.02; // 2%
    protected int $minAmount = 200000; // 200k VNĐ

    public function __construct()
    {
        $this->middleware(['auth', 'role:landlord']);
    }

    public function index(): View
    {
        $user = auth()->user();

        $requests = WithdrawalRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        $pendingAmount = WithdrawalRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        return view('landlord.withdrawals.index', [
            'balance' => $user->balance,
            'feePercent' => $this->feePercent * 100,
            'minAmount' => $this->minAmount,
            'pendingAmount' => $pendingAmount,
            'requests' => $requests,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . $this->minAmount,
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'note' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        if ($validated['amount'] > $user->balance) {
            return redirect()->back()->with('error', 'Số dư không đủ để rút.');
        }

        $fee = round($validated['amount'] * $this->feePercent, 2);
        $netAmount = $validated['amount'] - $fee;

        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'fee' => $fee,
            'net_amount' => $netAmount,
            'status' => 'pending',
            'payout_method' => 'bank_transfer',
            'payout_details' => [
                'bank_name' => $validated['bank_name'],
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
            ],
            'notes' => $validated['note'],
        ]);

        $user->decrement('balance', $validated['amount']);

        return redirect()
            ->route('landlord.withdrawals.index')
            ->with('success', "Đã tạo yêu cầu rút tiền #{$withdrawal->id}. Vui lòng chờ admin duyệt.");
    }

    public function cancel(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        abort_if($withdrawal->user_id !== $request->user()->id, 403);

        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể hủy yêu cầu đang chờ xử lý.');
        }

        $withdrawal->update([
            'status' => 'rejected',
            'notes' => trim(($withdrawal->notes ? $withdrawal->notes . PHP_EOL : '') . 'Người dùng đã hủy yêu cầu.'),
            'processed_at' => now(),
        ]);

        $withdrawal->user->increment('balance', $withdrawal->amount);

        return redirect()->back()->with('success', 'Yêu cầu rút tiền đã được hủy.');
    }
}

