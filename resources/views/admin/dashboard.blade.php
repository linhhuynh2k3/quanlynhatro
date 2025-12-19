@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Tổng người dùng</h6>
                    <h2 class="mb-0 text-primary">{{ $stats['total_users'] }}</h2>
                </div>
                <div class="stat-icon bg-primary">
                    <i class="bi bi-people text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Chủ trọ</h6>
                    <h2 class="mb-0 text-success">{{ $stats['total_landlords'] }}</h2>
                </div>
                <div class="stat-icon bg-success">
                    <i class="bi bi-person-badge text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Người tìm phòng</h6>
                    <h2 class="mb-0 text-info">{{ $stats['total_tenants'] }}</h2>
                </div>
                <div class="stat-icon bg-info">
                    <i class="bi bi-person text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Tổng bài đăng</h6>
                    <h2 class="mb-0 text-warning">{{ $stats['total_listings'] }}</h2>
                </div>
                <div class="stat-icon bg-warning">
                    <i class="bi bi-file-post text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Listing Status -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card border-start border-warning border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Chờ duyệt</h6>
                    <h2 class="mb-0 text-warning">{{ $stats['pending_listings'] }}</h2>
                </div>
                <div class="stat-icon bg-warning">
                    <i class="bi bi-clock-history text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Đã duyệt</h6>
                    <h2 class="mb-0 text-success">{{ $stats['approved_listings'] }}</h2>
                </div>
                <div class="stat-icon bg-success">
                    <i class="bi bi-check-circle text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-start border-danger border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Hết hạn</h6>
                    <h2 class="mb-0 text-danger">{{ $stats['expired_listings'] }}</h2>
                </div>
                <div class="stat-icon bg-danger">
                    <i class="bi bi-x-circle text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Stats -->
<div class="card mb-4">
    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <h5 class="mb-0"><i class="bi bi-graph-up"></i> Thống kê doanh thu</h5>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <h6 class="text-muted mb-2"><i class="bi bi-wallet2"></i> Tổng nạp tiền</h6>
                    <h4 class="text-success mb-0">{{ number_format($revenueStats['total_deposits']) }} VNĐ</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h6 class="text-muted mb-2"><i class="bi bi-credit-card"></i> Tổng thanh toán</h6>
                    <h4 class="text-primary mb-0">{{ number_format($revenueStats['total_listing_payments']) }} VNĐ</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h6 class="text-muted mb-2"><i class="bi bi-calendar-day"></i> Hôm nay</h6>
                    <h4 class="text-info mb-0">{{ number_format($revenueStats['today_revenue']) }} VNĐ</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h6 class="text-muted mb-2"><i class="bi bi-calendar-month"></i> Tháng này</h6>
                    <h4 class="text-warning mb-0">{{ number_format($revenueStats['month_revenue']) }} VNĐ</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Listings -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-file-post"></i> Bài đăng mới nhất</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Chủ trọ</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_listings as $listing)
                    <tr>
                        <td>
                            <strong>{{ Str::limit($listing->title, 50) }}</strong>
                        </td>
                        <td>{{ $listing->user->name }}</td>
                        <td>
                            @if($listing->status == 'approved')
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã duyệt</span>
                            @elseif($listing->status == 'pending')
                                <span class="badge bg-warning"><i class="bi bi-clock-history"></i> Chờ duyệt</span>
                            @elseif($listing->status == 'rejected')
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Từ chối</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($listing->status) }}</span>
                            @endif
                        </td>
                        <td>{{ \App\Helpers\DateTimeHelper::formatDate($listing->created_at) }}</td>
                        <td>
                            <a href="{{ route('admin.listings.show', $listing->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Unprocessed Feedback -->
@if($unprocessed_feedbacks->count() > 0)
<div class="card">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">
            <i class="bi bi-exclamation-triangle"></i>
            Phản hồi chưa xử lý ({{ $stats['unprocessed_feedback'] }})
        </h5>
    </div>
    <div class="card-body">
        <div class="list-group">
            @foreach($unprocessed_feedbacks as $feedback)
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $feedback->subject }}</h6>
                        <p class="mb-1 text-muted">
                            <i class="bi bi-person"></i> {{ $feedback->name }} - 
                            <i class="bi bi-envelope"></i> {{ $feedback->email }}
                        </p>
                        <p class="mb-0">{{ Str::limit($feedback->message, 150) }}</p>
                    </div>
                    <a href="{{ route('admin.feedback.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
