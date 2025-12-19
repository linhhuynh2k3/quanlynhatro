<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request): View
    {
        $query = WithdrawalRequest::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderByDesc('created_at')->paginate(20);

        $stats = [
            'pending' => WithdrawalRequest::where('status', 'pending')->sum('amount'),
            'processing' => WithdrawalRequest::where('status', 'processing')->sum('amount'),
            'approved' => WithdrawalRequest::where('status', 'approved')->sum('amount'),
            'rejected' => WithdrawalRequest::where('status', 'rejected')->sum('amount'),
        ];

        return view('admin.withdrawals.index', compact('requests', 'stats'));
    }

    public function show(WithdrawalRequest $withdrawal): View
    {
        $withdrawal->load(['user', 'processor']);

        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    public function updateStatus(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:processing,approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (in_array($withdrawal->status, ['approved', 'rejected'], true)) {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        if ($validated['action'] === 'processing') {
            $withdrawal->update([
                'status' => 'processing',
                'notes' => $validated['notes'] ?? $withdrawal->notes,
            ]);

            return redirect()->back()->with('success', 'Đã cập nhật trạng thái sang Đang xử lý.');
        }

        if ($validated['action'] === 'approved') {
            $withdrawal->update([
                'status' => 'approved',
                'notes' => $validated['notes'] ?? $withdrawal->notes,
                'processed_by' => $request->user()->id,
                'processed_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Đã xác nhận thanh toán cho yêu cầu rút tiền.');
        }

        // rejected
        $withdrawal->update([
            'status' => 'rejected',
            'notes' => $validated['notes'] ?? $withdrawal->notes,
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        $withdrawal->user->increment('balance', $withdrawal->amount);

        return redirect()->back()->with('success', 'Đã từ chối yêu cầu và hoàn tiền cho chủ trọ.');
    }
}

