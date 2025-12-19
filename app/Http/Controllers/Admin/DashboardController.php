<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $stats = [
            'total_users' => User::count(),
            'total_landlords' => User::where('role', 'landlord')->count(),
            'total_tenants' => User::where('role', 'tenant')->count(),
            'total_listings' => Listing::count(),
            'pending_listings' => Listing::where('status', 'pending')->count(),
            'approved_listings' => Listing::where('status', 'approved')->count(),
            'expired_listings' => Listing::where('status', 'expired')->count(),
            'unprocessed_feedback' => Feedback::where('is_resolved', false)->count(),
        ];

        // Thống kê doanh thu
        $revenueStats = [
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

        // Biểu đồ doanh thu 7 ngày gần nhất
        $revenueChart = Payment::where('type', 'listing_payment')
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Bài đăng mới nhất
        $recent_listings = Listing::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Phản hồi chưa xử lý
        $unprocessed_feedbacks = Feedback::where('is_resolved', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'revenueStats',
            'revenueChart',
            'recent_listings',
            'unprocessed_feedbacks'
        ));
    }
}
