@extends('layouts.admin')

@section('page-title', 'Chi tiết giao dịch #' . $payment->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
    <span class="text-muted">
        <i class="bi bi-clock-history"></i> {{ \App\Helpers\DateTimeHelper::formatDateTime($payment->created_at) }}
    </span>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Thông tin giao dịch</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Mã giao dịch</dt>
                    <dd class="col-sm-8">
                        <strong>#{{ $payment->id }}</strong>
                        @if($payment->transaction_code)
                            <span class="badge bg-light text-dark ms-2">{{ $payment->transaction_code }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Loại</dt>
                    <dd class="col-sm-8">
                        @if($payment->type === 'deposit')
                            <span class="badge bg-success"><i class="bi bi-wallet2"></i> Nạp tiền</span>
                        @elseif($payment->type === 'listing_payment')
                            <span class="badge bg-primary"><i class="bi bi-badge-ad"></i> Thanh toán bài đăng</span>
                        @elseif($payment->type === 'booking_payment')
                            <span class="badge bg-info text-dark"><i class="bi bi-house-door"></i> Thanh toán hợp đồng</span>
                        @else
                            <span class="badge bg-secondary">{{ $payment->type }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Tình trạng</dt>
                    <dd class="col-sm-8">
                        @if($payment->status === 'success')
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Thành công</span>
                        @elseif($payment->status === 'pending')
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Chờ xử lý</span>
                        @else
                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Thất bại</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Phương thức</dt>
                    <dd class="col-sm-8 text-capitalize">{{ $payment->method ?? 'wallet' }}</dd>

                    <dt class="col-sm-4">Số tiền</dt>
                    <dd class="col-sm-8">
                        <strong class="{{ $payment->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                            {{ number_format($payment->amount) }} VNĐ
                        </strong>
                    </dd>

                    <dt class="col-sm-4">Mô tả</dt>
                    <dd class="col-sm-8">{{ $payment->description ?? '—' }}</dd>

                    @if($payment->listing)
                        <dt class="col-sm-4">Bài đăng liên quan</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('admin.listings.show', $payment->listing_id) }}" class="link-primary">
                                #{{ $payment->listing_id }} - {{ $payment->listing->title }}
                            </a>
                        </dd>
                    @endif

                    @if($payment->contract)
                        <dt class="col-sm-4">Hợp đồng liên quan</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-light text-dark">Contract #{{ $payment->contract_id }}</span>
                        </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-circle"></i> Người tạo giao dịch</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $payment->user->name }}</h6>
                        <small class="text-muted d-block"><i class="bi bi-envelope"></i> {{ $payment->user->email }}</small>
                        <small class="text-muted d-block"><i class="bi bi-telephone"></i> {{ $payment->user->phone ?? 'Chưa cập nhật' }}</small>
                        <span class="badge bg-secondary mt-2 text-uppercase">{{ $payment->user->role }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($payment->creditedUser && $payment->credited_user_id !== $payment->user_id)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-wallet2"></i> Người nhận tiền</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $payment->creditedUser->name }}</h6>
                            <small class="text-muted d-block"><i class="bi bi-envelope"></i> {{ $payment->creditedUser->email }}</small>
                            <span class="badge bg-light text-dark mt-2">Số dư hiện tại: {{ number_format($payment->creditedUser->balance) }} VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

