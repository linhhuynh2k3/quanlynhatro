<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Mail\UtilityBillNotification;
use App\Models\Contract;
use App\Models\Listing;
use App\Models\UtilityBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoomManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:landlord']);
    }

    /**
     * Hiển thị danh sách phòng đang cho thuê
     */
    public function index(Request $request): View
    {
        $contracts = Contract::with(['listing', 'tenant', 'utilityBills'])
            ->where('landlord_id', auth()->id())
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('landlord.rooms.index', compact('contracts'));
    }

    /**
     * Hiển thị form tính tiền phòng
     */
    public function createBill(Contract $contract): View
    {
        $this->authorizeContract($contract);
        
        $contract->load(['listing', 'tenant']);
        
        // Lấy hóa đơn gần nhất để lấy số cũ
        $lastBill = UtilityBill::where('contract_id', $contract->id)
            ->orderBy('bill_date', 'desc')
            ->first();

        return view('landlord.rooms.create-bill', compact('contract', 'lastBill'));
    }

    /**
     * Lưu hóa đơn và gửi email
     */
    public function storeBill(Request $request, Contract $contract): RedirectResponse
    {
        $this->authorizeContract($contract);
        
        $validated = $request->validate([
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after:bill_date',
            'room_price' => 'required|numeric|min:0',
            'electricity_old_reading' => 'nullable|integer|min:0',
            'electricity_new_reading' => 'nullable|integer|min:0|gte:electricity_old_reading',
            'water_old_reading' => 'nullable|integer|min:0',
            'water_new_reading' => 'nullable|integer|min:0|gte:water_old_reading',
            'wifi_price' => 'nullable|numeric|min:0',
            'garbage_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $listing = $contract->listing;
        
        // Tính toán tiền điện
        $electricityUsage = 0;
        $electricityTotal = 0;
        if ($validated['electricity_new_reading'] && $validated['electricity_old_reading']) {
            $electricityUsage = $validated['electricity_new_reading'] - $validated['electricity_old_reading'];
            $electricityPricePerUnit = $listing->electricity_price ?? 0;
            $electricityTotal = $electricityUsage * $electricityPricePerUnit;
        }

        // Tính toán tiền nước
        $waterUsage = 0;
        $waterTotal = 0;
        if ($validated['water_new_reading'] && $validated['water_old_reading']) {
            $waterUsage = $validated['water_new_reading'] - $validated['water_old_reading'];
            $waterPricePerUnit = $listing->water_price ?? 0;
            $waterTotal = $waterUsage * $waterPricePerUnit;
        }

        // Lấy giá wifi và rác từ listing nếu không nhập
        $wifiPrice = $validated['wifi_price'] ?? $listing->wifi_price ?? 0;
        $garbagePrice = $validated['garbage_price'] ?? $listing->garbage_price ?? 0;

        // Tính tổng tiền
        $totalAmount = $validated['room_price'] + $electricityTotal + $waterTotal + $wifiPrice + $garbagePrice;

        // Tạo số hóa đơn
        $billNumber = 'HD-' . date('Ymd') . '-' . Str::random(6);

        // Tạo hóa đơn
        $bill = UtilityBill::create([
            'contract_id' => $contract->id,
            'listing_id' => $contract->listing_id,
            'landlord_id' => $contract->landlord_id,
            'tenant_id' => $contract->tenant_id,
            'bill_number' => $billNumber,
            'bill_date' => $validated['bill_date'],
            'due_date' => $validated['due_date'],
            'status' => 'pending',
            'room_price' => $validated['room_price'],
            'electricity_old_reading' => $validated['electricity_old_reading'],
            'electricity_new_reading' => $validated['electricity_new_reading'],
            'electricity_usage' => $electricityUsage,
            'electricity_price_per_unit' => $listing->electricity_price ?? 0,
            'electricity_total' => $electricityTotal,
            'water_old_reading' => $validated['water_old_reading'],
            'water_new_reading' => $validated['water_new_reading'],
            'water_usage' => $waterUsage,
            'water_price_per_unit' => $listing->water_price ?? 0,
            'water_total' => $waterTotal,
            'wifi_price' => $wifiPrice,
            'garbage_price' => $garbagePrice,
            'total_amount' => $totalAmount,
            'payment_status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Gửi email thông báo
        try {
            Mail::to($contract->tenant->email)->send(new UtilityBillNotification($bill));
        } catch (\Exception $e) {
            \Log::error('Failed to send utility bill email: ' . $e->getMessage());
        }

        return redirect()->route('landlord.rooms.index')
            ->with('success', 'Đã tạo hóa đơn và gửi thông báo đến người thuê.');
    }

    /**
     * Xem chi tiết hóa đơn
     */
    public function showBill(UtilityBill $bill): View
    {
        $this->authorizeBill($bill);
        
        $bill->load(['contract', 'listing', 'tenant', 'landlord']);
        
        return view('landlord.rooms.show-bill', compact('bill'));
    }

    protected function authorizeContract(Contract $contract): void
    {
        abort_unless($contract->landlord_id === auth()->id(), 403);
    }

    protected function authorizeBill(UtilityBill $bill): void
    {
        abort_unless($bill->landlord_id === auth()->id(), 403);
    }
}
