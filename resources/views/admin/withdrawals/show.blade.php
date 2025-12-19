@extends('layouts.admin')

@section('page-title', 'Chi tiết rút tiền #' . $withdrawal->id)

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Thông tin giao dịch</h5>
                <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Số tiền yêu cầu</small>
                        <h4 class="mb-0">{{ number_format($withdrawal->amount) }} VNĐ</h4>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Số tiền thực nhận</small>
                        <h4 class="text-success mb-0">{{ number_format($withdrawal->net_amount) }} VNĐ</h4>
                        <small class="text-muted">Phí: {{ number_format($withdrawal->fee) }} VNĐ</small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Chủ tài khoản</small>
                        <strong>{{ $withdrawal->payout_details['account_name'] ?? '—' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Ngân hàng</small>
                        <strong>{{ $withdrawal->payout_details['bank_name'] ?? '—' }}</strong>
                        <div class="text-muted small">{{ $withdrawal->payout_details['account_number'] ?? '' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Trạng thái</small>
                        @if($withdrawal->status === 'pending')
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @elseif($withdrawal->status === 'processing')
                            <span class="badge bg-info text-dark">Đang xử lý</span>
                        @elseif($withdrawal->status === 'approved')
                            <span class="badge bg-success">Đã chuyển</span>
                        @else
                            <span class="badge bg-danger">Từ chối</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Ngày tạo</small>
                        <div>{{ \App\Helpers\DateTimeHelper::formatDateTime($withdrawal->created_at) }}</div>
                    </div>
                </div>

                @if($withdrawal->notes)
                <div class="mt-4">
                    <small class="text-muted d-block">Ghi chú</small>
                    <div class="border rounded p-3 bg-light">{{ $withdrawal->notes }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">Chủ trọ</h6>
            </div>
            <div class="card-body">
                <div class="fw-bold">{{ $withdrawal->user->name }}</div>
                <div class="text-muted">{{ $withdrawal->user->email }}</div>
                <div class="text-muted">Số dư hiện tại: {{ number_format($withdrawal->user->balance) }} VNĐ</div>
            </div>
        </div>

        @if(!in_array($withdrawal->status, ['approved', 'rejected']))
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">Cập nhật trạng thái</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.withdrawals.update-status', $withdrawal) }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="action" value="processing">
                    <div class="mb-2">
                        <label class="form-label small text-muted">Ghi chú</label>
                        <textarea name="notes" rows="2" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-info w-100">Đánh dấu đang xử lý</button>
                </form>
                <form action="{{ route('admin.withdrawals.update-status', $withdrawal) }}" method="POST" class="mb-3" onsubmit="return confirm('Xác nhận đã chuyển tiền cho yêu cầu này?');">
                    @csrf
                    <input type="hidden" name="action" value="approved">
                    <div class="mb-2">
                        <label class="form-label small text-muted">Ghi chú</label>
                        <textarea name="notes" rows="2" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Xác nhận đã chuyển</button>
                </form>
                <form action="{{ route('admin.withdrawals.update-status', $withdrawal) }}" method="POST" onsubmit="return confirm('Từ chối yêu cầu và hoàn tiền cho chủ trọ?');">
                    @csrf
                    <input type="hidden" name="action" value="rejected">
                    <div class="mb-2">
                        <label class="form-label small text-muted">Lý do từ chối</label>
                        <textarea name="notes" rows="2" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Từ chối yêu cầu</button>
                </form>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">Thông tin xử lý</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Trạng thái</small>
                    <strong class="text-uppercase">{{ $withdrawal->status }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Người xử lý</small>
                    <strong>{{ $withdrawal->processor->name ?? '—' }}</strong>
                </div>
                <div>
                    <small class="text-muted d-block">Thời gian</small>
                    <strong>{{ $withdrawal->processed_at ? \App\Helpers\DateTimeHelper::formatDateTime($withdrawal->processed_at) : '—' }}</strong>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

