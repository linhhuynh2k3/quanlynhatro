@extends('layouts.admin')

@section('page-title', 'Yêu cầu rút tiền')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small mb-1">Chờ duyệt</div>
            <h4 class="mb-0 text-warning">{{ number_format($stats['pending']) }} VNĐ</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small mb-1">Đang xử lý</div>
            <h4 class="mb-0 text-info">{{ number_format($stats['processing']) }} VNĐ</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small mb-1">Đã chuyển</div>
            <h4 class="mb-0 text-success">{{ number_format($stats['approved']) }} VNĐ</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small mb-1">Từ chối</div>
            <h4 class="mb-0 text-danger">{{ number_format($stats['rejected']) }} VNĐ</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <form method="GET" class="d-flex align-items-center gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã chuyển</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
        </form>
        <span class="text-muted small">{{ $requests->total() }} yêu cầu</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Chủ trọ</th>
                        <th>Số tiền</th>
                        <th>Ngân hàng</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $request->user->name }}</div>
                            <small class="text-muted">{{ $request->user->email }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ number_format($request->amount) }} VNĐ</div>
                            <small class="text-muted">Nhận: {{ number_format($request->net_amount) }} VNĐ</small>
                        </td>
                        <td>
                            <small class="text-muted d-block">{{ $request->payout_details['bank_name'] ?? '—' }}</small>
                            <span class="small">{{ $request->payout_details['account_number'] ?? '' }}</span>
                        </td>
                        <td>
                            @if($request->status === 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @elseif($request->status === 'processing')
                                <span class="badge bg-info text-dark">Đang xử lý</span>
                            @elseif($request->status === 'approved')
                                <span class="badge bg-success">Đã chuyển</span>
                            @else
                                <span class="badge bg-danger">Từ chối</span>
                            @endif
                        </td>
                        <td>{{ \App\Helpers\DateTimeHelper::formatDateTime($request->created_at) }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.withdrawals.show', $request) }}" class="btn btn-outline-primary btn-sm">
                                Xem
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có yêu cầu nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $requests->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

