<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:landlord');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::where('landlord_id', Auth::id())
            ->with(['contract', 'tenant', 'contract.listing']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('contract_id')) {
            $query->where('contract_id', $request->contract_id);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);
        $contracts = Contract::where('landlord_id', Auth::id())
            ->where('status', 'active')
            ->with('tenant', 'listing')
            ->get();

        return view('landlord.invoices.index', compact('invoices', 'contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $contractId = $request->get('contract_id');
        $contract = Contract::where('landlord_id', Auth::id())
            ->where('status', 'active')
            ->with('tenant', 'listing')
            ->findOrFail($contractId);

        return view('landlord.invoices.create', compact('contract'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'rent_amount' => 'required|numeric|min:0',
            'electricity_old_reading' => 'nullable|integer|min:0',
            'electricity_new_reading' => 'nullable|integer|min:0|gte:electricity_old_reading',
            'electricity_unit_price' => 'nullable|numeric|min:0',
            'water_old_reading' => 'nullable|integer|min:0',
            'water_new_reading' => 'nullable|integer|min:0|gte:water_old_reading',
            'water_unit_price' => 'nullable|numeric|min:0',
            'wifi_amount' => 'nullable|numeric|min:0',
            'trash_amount' => 'nullable|numeric|min:0',
            'other_services_amount' => 'nullable|numeric|min:0',
            'other_services_note' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $contract = Contract::where('landlord_id', Auth::id())
            ->findOrFail($validated['contract_id']);

        // Calculate electricity amount
        $electricityAmount = 0;
        if ($validated['electricity_new_reading'] && $validated['electricity_old_reading']) {
            $units = $validated['electricity_new_reading'] - $validated['electricity_old_reading'];
            $electricityAmount = $units * ($validated['electricity_unit_price'] ?? 0);
        }

        // Calculate water amount
        $waterAmount = 0;
        if ($validated['water_new_reading'] && $validated['water_old_reading']) {
            $units = $validated['water_new_reading'] - $validated['water_old_reading'];
            $waterAmount = $units * ($validated['water_unit_price'] ?? 0);
        }

        // Calculate total
        $totalAmount = $validated['rent_amount']
            + $electricityAmount
            + $waterAmount
            + ($validated['wifi_amount'] ?? 0)
            + ($validated['trash_amount'] ?? 0)
            + ($validated['other_services_amount'] ?? 0);

        $invoice = Invoice::create([
            'contract_id' => $contract->id,
            'landlord_id' => Auth::id(),
            'tenant_id' => $contract->tenant_id,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'rent_amount' => $validated['rent_amount'],
            'electricity_old_reading' => $validated['electricity_old_reading'] ?? null,
            'electricity_new_reading' => $validated['electricity_new_reading'] ?? null,
            'electricity_unit_price' => $validated['electricity_unit_price'] ?? null,
            'electricity_amount' => $electricityAmount,
            'water_old_reading' => $validated['water_old_reading'] ?? null,
            'water_new_reading' => $validated['water_new_reading'] ?? null,
            'water_unit_price' => $validated['water_unit_price'] ?? null,
            'water_amount' => $waterAmount,
            'wifi_amount' => $validated['wifi_amount'] ?? 0,
            'trash_amount' => $validated['trash_amount'] ?? 0,
            'other_services_amount' => $validated['other_services_amount'] ?? 0,
            'other_services_note' => $validated['other_services_note'] ?? null,
            'total_amount' => $totalAmount,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('landlord.invoices.show', $invoice->id)
            ->with('success', 'Hóa đơn đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::where('landlord_id', Auth::id())
            ->with(['contract', 'tenant', 'contract.listing'])
            ->findOrFail($id);

        return view('landlord.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::where('landlord_id', Auth::id())
            ->with(['contract', 'tenant', 'contract.listing'])
            ->findOrFail($id);

        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Không thể chỉnh sửa hóa đơn đã thanh toán.');
        }

        return view('landlord.invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::where('landlord_id', Auth::id())->findOrFail($id);

        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Không thể chỉnh sửa hóa đơn đã thanh toán.');
        }

        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'rent_amount' => 'required|numeric|min:0',
            'electricity_old_reading' => 'nullable|integer|min:0',
            'electricity_new_reading' => 'nullable|integer|min:0|gte:electricity_old_reading',
            'electricity_unit_price' => 'nullable|numeric|min:0',
            'water_old_reading' => 'nullable|integer|min:0',
            'water_new_reading' => 'nullable|integer|min:0|gte:water_old_reading',
            'water_unit_price' => 'nullable|numeric|min:0',
            'wifi_amount' => 'nullable|numeric|min:0',
            'trash_amount' => 'nullable|numeric|min:0',
            'other_services_amount' => 'nullable|numeric|min:0',
            'other_services_note' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Recalculate amounts
        $electricityAmount = 0;
        if ($validated['electricity_new_reading'] && $validated['electricity_old_reading']) {
            $units = $validated['electricity_new_reading'] - $validated['electricity_old_reading'];
            $electricityAmount = $units * ($validated['electricity_unit_price'] ?? 0);
        }

        $waterAmount = 0;
        if ($validated['water_new_reading'] && $validated['water_old_reading']) {
            $units = $validated['water_new_reading'] - $validated['water_old_reading'];
            $waterAmount = $units * ($validated['water_unit_price'] ?? 0);
        }

        $totalAmount = $validated['rent_amount']
            + $electricityAmount
            + $waterAmount
            + ($validated['wifi_amount'] ?? 0)
            + ($validated['trash_amount'] ?? 0)
            + ($validated['other_services_amount'] ?? 0);

        $invoice->update([
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'rent_amount' => $validated['rent_amount'],
            'electricity_old_reading' => $validated['electricity_old_reading'] ?? null,
            'electricity_new_reading' => $validated['electricity_new_reading'] ?? null,
            'electricity_unit_price' => $validated['electricity_unit_price'] ?? null,
            'electricity_amount' => $electricityAmount,
            'water_old_reading' => $validated['water_old_reading'] ?? null,
            'water_new_reading' => $validated['water_new_reading'] ?? null,
            'water_unit_price' => $validated['water_unit_price'] ?? null,
            'water_amount' => $waterAmount,
            'wifi_amount' => $validated['wifi_amount'] ?? 0,
            'trash_amount' => $validated['trash_amount'] ?? 0,
            'other_services_amount' => $validated['other_services_amount'] ?? 0,
            'other_services_note' => $validated['other_services_note'] ?? null,
            'total_amount' => $totalAmount,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('landlord.invoices.show', $invoice->id)
            ->with('success', 'Hóa đơn đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::where('landlord_id', Auth::id())->findOrFail($id);

        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Không thể xóa hóa đơn đã thanh toán.');
        }

        $invoice->delete();

        return redirect()->route('landlord.invoices.index')
            ->with('success', 'Hóa đơn đã được xóa thành công.');
    }
}
