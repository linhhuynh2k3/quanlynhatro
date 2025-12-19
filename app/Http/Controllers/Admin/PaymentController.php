<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    public function index(Request $request)
    {
        $query = Payment::with(['user', 'listing']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.payments.index', compact('payments'));
    }

    public function revenueStats()
    {
        $stats = [
            'total_deposits' => Payment::where('type', 'deposit')
                ->where('status', 'success')
                ->sum('amount'),
            'total_listing_payments' => Payment::where('type', 'listing_payment')
                ->where('status', 'success')
                ->sum('amount'),
            'today_revenue' => Payment::where('type', 'listing_payment')
                ->where('status', 'success')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'month_revenue' => Payment::where('type', 'listing_payment')
                ->where('status', 'success')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        // Biểu đồ 30 ngày gần nhất
        $chartData = Payment::where('type', 'listing_payment')
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.payments.revenue', compact('stats', 'chartData'));
    }

    public function show($id)
    {
        $payment = Payment::with(['user', 'listing'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }
}
