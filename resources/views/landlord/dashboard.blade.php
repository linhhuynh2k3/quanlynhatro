@extends('layouts.landlord')

@section('page-title', 'Dashboard')


@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Số dư tài khoản</h6>
                    <h2 class="mb-0 text-success">{{ number_format(auth()->user()->balance) }} VNĐ</h2>
                </div>
                <div class="stat-icon bg-success">
                    
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('landlord.payments.index') }}" class="btn btn-success btn-sm w-100">
                     Nạp tiền
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Tổng bài đăng</h6>
                    <h2 class="mb-0 text-primary">{{ auth()->user()->listings()->count() }}</h2>
                </div>
                <div class="stat-icon bg-primary">
                    
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('landlord.listings.index') }}" class="btn btn-primary btn-sm w-100">
                     Xem tất cả
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Đang hoạt động</h6>
                    <h2 class="mb-0 text-info">{{ auth()->user()->listings()->where('status', 'approved')->where('expired_at', '>', now())->count() }}</h2>
                </div>
                <div class="stat-icon bg-info">
                    
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('landlord.listings.create') }}" class="btn btn-info btn-sm w-100">
                     Đăng bài mới
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-4">
    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);">
        <h5 class="mb-0"> Thao tác nhanh</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('landlord.listings.create') }}" class="btn btn-primary w-100 py-3">
                    
                    <strong>Đăng bài mới</strong>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('landlord.payments.index') }}" class="btn btn-success w-100 py-3">
                    
                    <strong>Nạp tiền</strong>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('landlord.listings.index') }}" class="btn btn-info w-100 py-3">
                    
                    <strong>Quản lý bài đăng</strong>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Listings -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"> Bài đăng gần đây</h5>
        <a href="{{ route('landlord.listings.index') }}" class="btn btn-sm btn-outline-primary">
            Xem tất cả 
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Trạng thái</th>
                        <th>Giá</th>
                        <th>Ngày đăng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(auth()->user()->listings()->latest()->limit(5)->get() as $listing)
                    <tr>
                        <td>
                            <strong>{{ Str::limit($listing->title, 50) }}</strong>
                        </td>
                        <td>
                            @if($listing->status == 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @elseif($listing->status == 'pending')
                                <span class="badge bg-warning">Chờ duyệt</span>
                            @elseif($listing->status == 'rejected')
                                <span class="badge bg-danger">Từ chối</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($listing->status) }}</span>
                            @endif
                        </td>
                        <td><strong>{{ number_format($listing->price) }} VNĐ</strong></td>
                        <td>{{ \App\Helpers\DateTimeHelper::formatDate($listing->created_at) }}</td>
                        <td>
                            <a href="{{ route('landlord.listings.show', $listing->id) }}" class="btn btn-sm btn-primary">
                                 Xem
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            
                            Chưa có bài đăng nào. <a href="{{ route('landlord.listings.create') }}">Đăng bài ngay!</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
