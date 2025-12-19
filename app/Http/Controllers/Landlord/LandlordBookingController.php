<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandlordBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:landlord']);
    }

    public function index(Request $request): View
    {
        $query = Contract::with(['listing', 'tenant', 'payment'])
            ->where('landlord_id', $request->user()->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $contracts = $query->orderByDesc('created_at')->paginate(15);

        return view('landlord.bookings.index', compact('contracts'));
    }

    public function show(Contract $contract): View
    {
        $this->authorizeContract($contract);

        $contract->load(['listing', 'tenant', 'payment']);

        return view('landlord.bookings.show', compact('contract'));
    }

    public function approve(Request $request, Contract $contract): RedirectResponse
    {
        $this->authorizeContract($contract);

        if ($contract->approval_status !== 'pending') {
            return back()->with('error', 'Hợp đồng này đã được xử lý.');
        }

        if ($contract->status === 'cancelled') {
            return back()->with('error', 'Hợp đồng đã bị hủy.');
        }

        $contract->update([
            'approval_status' => 'approved',
        ]);

        return back()->with('success', 'Đã duyệt yêu cầu. Người thuê có thể thanh toán.');
    }

    public function reject(Request $request, Contract $contract): RedirectResponse
    {
        $this->authorizeContract($contract);
        $contract->loadMissing('listing');

        if ($contract->approval_status !== 'pending') {
            return back()->with('error', 'Hợp đồng này đã được xử lý.');
        }

        if ($contract->reserved_at && $contract->listing) {
            $contract->listing->increment('available_units');
        }

        $contract->update([
            'approval_status' => 'rejected',
            'status' => 'cancelled',
            'reserved_at' => null,
        ]);

        return back()->with('success', 'Đã từ chối đơn đặt thuê.');
    }

    public function activate(Request $request, Contract $contract): RedirectResponse
    {
        $this->authorizeContract($contract);
        $contract->loadMissing('listing');

        if ($contract->status !== 'pending' || $contract->payment_status !== 'paid' || $contract->approval_status !== 'approved') {
            return redirect()->back()->with('error', 'Chỉ có thể ký các hợp đồng đã được duyệt và thanh toán.');
        }

        if (!$contract->listing) {
            return back()->with('error', 'Không tìm thấy bài đăng.');
        }

        $updateData = [
            'status' => 'active',
            'signed_at' => now(),
        ];

        if (!$contract->reserved_at) {
            if ($contract->listing->available_units < 1) {
                return back()->with('error', 'Bài đăng này đã hết phòng.');
            }

            $contract->listing->decrement('available_units');
            $updateData['reserved_at'] = now();
        }

        $contract->update($updateData);

        return redirect()->route('landlord.bookings.show', $contract)
            ->with('success', 'Đã ký hợp đồng và chuyển sang trạng thái hoạt động.');
    }

    protected function authorizeContract(Contract $contract): void
    {
        abort_unless($contract->landlord_id === auth()->id(), 403);
    }
}

