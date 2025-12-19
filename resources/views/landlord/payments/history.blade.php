@extends('layouts.landlord')

@section('page-title', 'Lịch sử giao dịch')


@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-receipt-cutoff me-2"></i> Lịch sử giao dịch</h5>
        <a href="{{ route('landlord.payments.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Quay lại nạp tiền
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('landlord.payments.history') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Loại giao dịch</label>
                    <select name="type" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Nạp tiền</option>
                        <option value="listing_payment" {{ request('type') == 'listing_payment' ? 'selected' : '' }}>Thanh toán bài đăng</option>
                        <option value="booking_payment" {{ request('type') == 'booking_payment' ? 'selected' : '' }}>Thanh toán thuê</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Thành công</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Phương thức</label>
                    <select name="method" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="vnpay" {{ request('method') == 'vnpay' ? 'selected' : '' }}>VNPAY</option>
                        <option value="wallet" {{ request('method') == 'wallet' ? 'selected' : '' }}>Ví nội bộ</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Lọc kết quả
                    </button>
                </div>
            </div>
        </form>

        @php
            $typeLabels = [
                'deposit' => 'Nạp tiền',
                'listing_payment' => 'Thanh toán bài đăng',
                'booking_payment' => 'Thanh toán thuê',
                'withdrawal' => 'Rút tiền',
            ];
        @endphp
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã giao dịch</th>
                        <th>Loại</th>
                        <th>Mô tả</th>
                        <th>Phương thức</th>
                        <th>Số tiền</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $payment->transaction_code ?? '—' }}</span>
                        </td>
                        <td>
                            @php
                                $isIncome = $payment->type === 'deposit';
                                $typeLabel = $typeLabels[$payment->type] ?? ucfirst($payment->type);
                            @endphp
                            <span class="badge {{ $isIncome ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }}">
                                <i class="bi {{ $isIncome ? 'bi-arrow-down-circle' : 'bi-arrow-up-circle' }} me-1"></i> {{ $typeLabel }}
                            </span>
                        </td>
                        <td>{{ $payment->description ?? 'Không có mô tả' }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary text-uppercase">
                                {{ $payment->method ?? 'wallet' }}
                            </span>
                        </td>
                        <td>
                            <strong class="{{ $payment->type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                {{ $payment->type == 'deposit' ? '+' : '-' }}{{ number_format($payment->amount) }} VNĐ
                            </strong>
                        </td>
                        <td>
                            @if($payment->status == 'success')
                                <span class="badge bg-success">
                                    <i class="bi bi-check2-circle me-1"></i> Thành công
                                </span>
                            @elseif($payment->status == 'pending')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i> Chờ xử lý
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i> Thất bại
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ \App\Helpers\DateTimeHelper::formatDateTime($payment->created_at) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có giao dịch nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $payments->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
