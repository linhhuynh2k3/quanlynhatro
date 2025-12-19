@extends('layouts.admin')

@section('page-title', 'Quản lý thanh toán')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-credit-card"></i> Quản lý thanh toán</h5>
        <a href="{{ route('admin.payments.revenue') }}" class="btn btn-success">
            <i class="bi bi-graph-up"></i> Thống kê doanh thu
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('admin.payments.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">Tất cả loại</option>
                        <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Nạp tiền</option>
                        <option value="listing_payment" {{ request('type') == 'listing_payment' ? 'selected' : '' }}>Thanh toán bài đăng</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Thành công</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           class="form-control" 
                           placeholder="Tìm kiếm theo tên, email...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                </div>
            </div>
        </form>

        <!-- Payments Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Loại</th>
                        <th>Mô tả</th>
                        <th>Số tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><strong>#{{ $payment->id }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $payment->user->name }}</div>
                                    <small class="text-muted"><i class="bi bi-envelope"></i> {{ $payment->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($payment->type == 'deposit')
                                <span class="badge bg-success">
                                    <i class="bi bi-wallet2"></i> Nạp tiền
                                </span>
                            @else
                                <span class="badge bg-primary">
                                    <i class="bi bi-credit-card"></i> Thanh toán
                                </span>
                            @endif
                        </td>
                        <td>{{ Str::limit($payment->description, 40) }}</td>
                        <td>
                            <strong class="{{ $payment->type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                {{ $payment->type == 'deposit' ? '+' : '-' }}{{ number_format($payment->amount) }} VNĐ
                            </strong>
                        </td>
                        <td>
                            @if($payment->status == 'success')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Thành công
                                </span>
                            @elseif($payment->status == 'pending')
                                <span class="badge bg-warning">
                                    <i class="bi bi-clock-history"></i> Chờ xử lý
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle"></i> Thất bại
                                </span>
                            @endif
                        </td>
                        <td>
                            <small>
                                <i class="bi bi-calendar3"></i> {{ \App\Helpers\DateTimeHelper::formatDate($payment->created_at) }}<br>
                                <i class="bi bi-clock"></i> {{ \App\Helpers\DateTimeHelper::formatTime($payment->created_at) }}
                            </small>
                        </td>
                        <td>
                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có giao dịch nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $payments->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
