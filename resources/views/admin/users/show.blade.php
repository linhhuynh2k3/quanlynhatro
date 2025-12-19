@extends('layouts.admin')

@section('page-title', 'Chi tiết người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
    <div class="btn-group">
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil-square"></i> Chỉnh sửa
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 90px; height: 90px;">
                    <i class="bi bi-person fs-2"></i>
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <span class="badge bg-dark text-uppercase">{{ $user->role }}</span>
                <p class="text-muted mt-2 mb-0">{{ $user->email }}</p>
                <p class="text-muted mb-3">{{ $user->phone ?? 'Chưa cập nhật số điện thoại' }}</p>
                <div class="border-top pt-3">
                    <small class="text-muted d-block mb-1">Số dư ví</small>
                    <h5 class="text-success">{{ number_format($user->balance) }} VNĐ</h5>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin chung</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Ngày tạo</dt>
                    <dd class="col-7">{{ \App\Helpers\DateTimeHelper::formatDateTime($user->created_at) }}</dd>

                    <dt class="col-5">Lần cập nhật</dt>
                    <dd class="col-7">{{ \App\Helpers\DateTimeHelper::formatDateTime($user->updated_at) }}</dd>

                    <dt class="col-5">Trạng thái email</dt>
                    <dd class="col-7">
                        @if($user->hasVerifiedEmail())
                            <span class="badge bg-success">Đã xác thực</span>
                        @else
                            <span class="badge bg-warning text-dark">Chưa xác thực</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-house-door"></i> Bài đăng gần đây</h5>
                <span class="badge bg-primary">{{ $user->listings->count() }} bài đăng</span>
            </div>
            <div class="card-body">
                @forelse($user->listings->take(5) as $listing)
                    <div class="border-bottom pb-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $listing->title }}</strong>
                            <span class="badge bg-light text-dark text-uppercase">{{ $listing->status }}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-geo-alt"></i> {{ $listing->address ?? 'Chưa cập nhật' }} |
                            <i class="bi bi-clock-history"></i> {{ \App\Helpers\DateTimeHelper::formatDate($listing->created_at) }}
                        </small>
                    </div>
                @empty
                    <p class="text-muted mb-0">Người dùng này chưa có bài đăng.</p>
                @endforelse
                @if($user->listings->count() > 5)
                    <small class="text-muted">... và {{ $user->listings->count() - 5 }} bài đăng khác.</small>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-credit-card"></i> Giao dịch gần đây</h5>
                <span class="badge bg-info text-dark">{{ $user->payments->count() }} giao dịch</span>
            </div>
            <div class="card-body">
                @forelse($user->payments->take(5) as $payment)
                    <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                        <div>
                            <strong>#{{ $payment->id }}</strong>
                            <div class="text-muted small">
                                {{ ucfirst(str_replace('_', ' ', $payment->type)) }} • {{ \App\Helpers\DateTimeHelper::formatDateTime($payment->created_at) }}
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="d-block {{ $payment->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                {{ $payment->type === 'deposit' ? '+' : '-' }}{{ number_format($payment->amount) }} VNĐ
                            </span>
                            <span class="badge {{ $payment->status === 'success' ? 'bg-success' : ($payment->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Chưa có giao dịch nào.</p>
                @endforelse
                @if($user->payments->count() > 5)
                    <small class="text-muted">... và {{ $user->payments->count() - 5 }} giao dịch khác.</small>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

