@extends('layouts.landlord')

@section('page-title', 'Chi tiết đơn thuê #' . $contract->id)

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Thông tin hợp đồng</h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-secondary text-uppercase">{{ $contract->status }}</span>
                    <span class="badge 
                        @if($contract->approval_status === 'approved') bg-success
                        @elseif($contract->approval_status === 'rejected') bg-danger
                        @else bg-warning text-dark
                        @endif">
                        {{ strtoupper($contract->approval_status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Bài đăng</dt>
                    <dd class="col-sm-8">
                        <a href="{{ route('listings.show', $contract->listing_id) }}" target="_blank">
                            {{ $contract->listing->title }}
                        </a>
                    </dd>

                    <dt class="col-sm-4 text-muted">Người thuê</dt>
                    <dd class="col-sm-8">
                        <strong>{{ $contract->tenant->name }}</strong><br>
                        <small class="text-muted">{{ $contract->tenant->email }}</small>
                    </dd>

                    <dt class="col-sm-4 text-muted">Thời gian thuê</dt>
                    <dd class="col-sm-8">
                        {{ \App\Helpers\DateTimeHelper::formatDate($contract->start_date) }} - {{ \App\Helpers\DateTimeHelper::formatDate($contract->end_date) }}
                    </dd>

                    <dt class="col-sm-4 text-muted">Giá mỗi tháng</dt>
                    <dd class="col-sm-8">
                        <strong>{{ number_format($contract->monthly_price) }} VNĐ</strong>
                    </dd>

                    <dt class="col-sm-4 text-muted">Ghi chú</dt>
                    <dd class="col-sm-8">{{ $contract->notes ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted">Thanh toán</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-info text-uppercase">{{ $contract->payment_status }}</span>
                        @if($contract->payment && $contract->payment->transaction_code)
                            <div class="text-muted small">Mã giao dịch: {{ $contract->payment->transaction_code }}</div>
                        @endif
                    </dd>
                    <dt class="col-sm-4 text-muted">Phòng còn</dt>
                    <dd class="col-sm-8">
                        {{ $contract->listing->available_units }} / {{ $contract->listing->total_units }}
                    </dd>
                </dl>
            </div>
        </div>

        @if($contract->approval_status === 'pending')
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-exclamation-circle me-2"></i> Chờ phê duyệt</h6>
            </div>
            <div class="card-body d-flex flex-wrap gap-2">
                <form action="{{ route('landlord.bookings.approve', $contract) }}" method="POST" onsubmit="return confirm('Duyệt đơn này?')">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Duyệt đơn
                    </button>
                </form>
                <form action="{{ route('landlord.bookings.reject', $contract) }}" method="POST" onsubmit="return confirm('Từ chối đơn này?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-x-lg me-1"></i> Từ chối
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">Thông tin liên hệ</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Người thuê:</strong><br>{{ $contract->tenant->name }}</p>
                <p class="mb-2"><strong>Email:</strong><br>{{ $contract->tenant->email }}</p>
            </div>
        </div>

        <a href="{{ route('landlord.bookings.index') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>
@endsection

