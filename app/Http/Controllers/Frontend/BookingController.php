<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContractSignedMail;
use App\Models\Contract;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:tenant');
    }

    public function index()
    {
        $contracts = Contract::where('tenant_id', auth()->id())
            ->with(['listing', 'landlord'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('frontend.bookings.index', compact('contracts'));
    }

    public function store(Request $request, $listingId)
    {
        $listing = Listing::where('status', 'approved')
            ->where('expired_at', '>', now())
            ->findOrFail($listingId);

        // Kiểm tra xem đã có hợp đồng pending chưa
        $existingContract = Contract::where('listing_id', $listingId)
            ->where('tenant_id', auth()->id())
            ->whereIn('status', ['pending', 'active'])
            ->first();

        if ($existingContract) {
            return redirect()->back()
                ->with('error', 'Bạn đã đặt thuê bài đăng này rồi.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'rental_period' => 'required|in:3,6,9,12',
            'end_date' => 'required|date|after:start_date',
            'note' => 'nullable|string|max:1000',
        ]);
        
        // Tính lại end_date từ start_date và rental_period
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $months = (int)$validated['rental_period'];
        $endDate = $startDate->copy()->addMonths($months);
        $validated['end_date'] = $endDate->format('Y-m-d');

        if ($listing->available_units < 1) {
            return back()->with('error', 'Bài đăng này đã hết phòng trống.');
        }

        Contract::create([
            'listing_id' => $listing->id,
            'landlord_id' => $listing->user_id,
            'tenant_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'monthly_price' => $listing->price,
            'status' => 'pending',
            'approval_status' => 'pending',
            'payment_status' => 'unpaid',
            'notes' => $validated['note'] ?? null,
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Yêu cầu đặt thuê đã được gửi. Vui lòng chờ chủ trọ xác nhận.');
    }

    public function sign(Request $request, $contractId)
    {
        $contract = Contract::where('tenant_id', auth()->id())
            ->with('listing')
            ->findOrFail($contractId);

        if ($contract->approval_status !== 'approved') {
            return back()->with('error', 'Chủ trọ chưa duyệt hợp đồng này.');
        }

        if ($contract->payment_status !== 'paid') {
            return back()->with('error', 'Vui lòng thanh toán trước khi ký hợp đồng.');
        }

        if ($contract->status === 'active') {
            return back()->with('error', 'Hợp đồng này đã được ký.');
        }

        $data = $request->validate([
            'signature_name' => ['required', 'string', 'max:255'],
            'agree_terms' => ['required', 'accepted'],
        ]);

        if (!$contract->listing) {
            return back()->with('error', 'Không tìm thấy thông tin bài đăng.');
        }

        $updateData = [
            'status' => 'active',
            'signed_at' => now(),
            'signature_name' => $data['signature_name'],
            'signature_data' => $data['signature_name'],
            'terms_accepted_at' => now(),
        ];

        if (!$contract->reserved_at) {
            if ($contract->listing->available_units < 1) {
                return back()->with('error', 'Bài đăng hiện không còn phòng trống.');
            }

            $contract->listing->decrement('available_units');
            $updateData['reserved_at'] = now();
        }

        $contract->update($updateData);

        try {
            Mail::to($contract->tenant->email)
                ->send(new ContractSignedMail($contract));
        } catch (\Throwable $th) {
            report($th);
        }

        return redirect()->route('bookings.index')->with('success', 'Hợp đồng đã được ký thành công.');
    }
}
