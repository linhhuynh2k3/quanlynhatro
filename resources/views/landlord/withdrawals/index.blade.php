@extends('layouts.landlord')

@section('page-title', 'Rút tiền')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Số dư khả dụng</span>
                <i class="bi bi-wallet2 text-success fs-4"></i>
            </div>
            <h3 class="mb-1">{{ number_format($balance) }} VNĐ</h3>
            <small class="text-muted">Đã trừ các yêu cầu rút đang chờ</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Đang chờ xử lý</span>
                <i class="bi bi-hourglass-split text-warning fs-4"></i>
            </div>
            <h3 class="mb-1">{{ number_format($pendingAmount) }} VNĐ</h3>
            <small class="text-muted">Bao gồm trạng thái pending & processing</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Phí rút tiền</span>
                <i class="bi bi-percent text-info fs-4"></i>
            </div>
            <h3 class="mb-1">{{ $feePercent }}%</h3>
            <small class="text-muted">Tối thiểu {{ number_format($minAmount) }} VNĐ mỗi lần</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Trạng thái gần đây</span>
                <i class="bi bi-bell text-primary fs-4"></i>
            </div>
            <h3 class="mb-1">{{ $requests->first()?->status ? ucfirst($requests->first()->status) : '—' }}</h3>
            <small class="text-muted">Đơn mới nhất</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i> Tạo yêu cầu rút tiền</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('landlord.withdrawals.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số tiền cần rút</label>
                        <div class="input-group">
                            <input type="number" min="{{ $minAmount }}" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" placeholder="Nhập số tiền (VNĐ)" required>
                            <span class="input-group-text">VNĐ</span>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">Tối thiểu {{ number_format($minAmount) }} VNĐ. Phí {{ $feePercent }}% sẽ được trừ trực tiếp.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ngân hàng</label>
                        <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" placeholder="VD: Vietcombank" value="{{ old('bank_name') }}" required>
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chủ tài khoản</label>
                        <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror" placeholder="Tên in trên tài khoản" value="{{ old('account_name', auth()->user()->name) }}" required>
                        @error('account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số tài khoản</label>
                        <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" placeholder="Nhập số tài khoản" value="{{ old('account_number') }}" required>
                        @error('account_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú (tuỳ chọn)</label>
                        <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror" placeholder="VD: Rút tiền thanh toán chi phí...">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-send me-1"></i> Gửi yêu cầu
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lịch sử rút tiền</h5>
                <span class="text-muted small">{{ $requests->total() }} yêu cầu</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Số tiền</th>
                                <th>Phương thức</th>
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
                                    <div class="fw-semibold">{{ number_format($request->amount) }} VNĐ</div>
                                    <small class="text-muted">Nhận: {{ number_format($request->net_amount) }} VNĐ</small>
                                </td>
                                <td>
                                    <small class="text-muted d-block">{{ $request->payout_details['bank_name'] ?? 'Ngân hàng' }}</small>
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
                                <td>
                                    <div>{{ \App\Helpers\DateTimeHelper::formatDate($request->created_at) }}</div>
                                    <small class="text-muted">{{ \App\Helpers\DateTimeHelper::formatTime($request->created_at) }}</small>
                                </td>
                                <td class="text-end">
                                    @if($request->status === 'pending')
                                    <form action="{{ route('landlord.withdrawals.cancel', $request) }}" method="POST" onsubmit="return confirm('Hủy yêu cầu rút tiền này?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Chưa có yêu cầu rút tiền nào
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($requests->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $requests->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

