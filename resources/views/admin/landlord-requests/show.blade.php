@extends('layouts.admin')

@section('page-title', 'Chi tiết yêu cầu chủ trọ')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Yêu cầu #{{ $landlordRequest->id }}</h5>
                    <small class="text-muted">Gửi lúc {{ \App\Helpers\DateTimeHelper::formatDateTime($landlordRequest->created_at) }}</small>
                </div>
                @php
                    $statusClasses = [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    ];
                    $statusLabels = [
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Đã từ chối',
                    ];
                @endphp
                <span class="badge bg-{{ $statusClasses[$landlordRequest->status] ?? 'secondary' }} px-3 py-2">
                    {{ $statusLabels[$landlordRequest->status] ?? ucfirst($landlordRequest->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Họ và tên</label>
                        <p class="fw-semibold mb-0">{{ $landlordRequest->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0"><i class="bi bi-envelope text-muted"></i> {{ $landlordRequest->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Số điện thoại</label>
                        <p class="mb-0"><i class="bi bi-telephone text-muted"></i> {{ $landlordRequest->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Địa chỉ</label>
                        <p class="mb-0">{{ $landlordRequest->address }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Số CCCD/CMND</label>
                        <p class="mb-0">{{ $landlordRequest->cccd_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Tin nhắn</label>
                        <div class="border rounded p-3 bg-light" style="white-space: pre-line;">
                            {{ $landlordRequest->message ?? 'Không có ghi chú bổ sung.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CCCD Images -->
        @if($landlordRequest->cccd_front_image || $landlordRequest->cccd_back_image)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-card-image"></i> Ảnh CCCD/CMND</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($landlordRequest->cccd_front_image)
                    <div class="col-md-6">
                        <label class="text-muted small d-block mb-2">Mặt trước</label>
                        <img src="{{ \App\Helpers\ImageHelper::url($landlordRequest->cccd_front_image) }}" 
                             alt="CCCD mặt trước" 
                             class="img-fluid rounded border" 
                             style="max-height: 300px; cursor: pointer;"
                             onclick="window.open(this.src, '_blank')">
                    </div>
                    @endif
                    @if($landlordRequest->cccd_back_image)
                    <div class="col-md-6">
                        <label class="text-muted small d-block mb-2">Mặt sau</label>
                        <img src="{{ \App\Helpers\ImageHelper::url($landlordRequest->cccd_back_image) }}" 
                             alt="CCCD mặt sau" 
                             class="img-fluid rounded border" 
                             style="max-height: 300px; cursor: pointer;"
                             onclick="window.open(this.src, '_blank')">
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Business License -->
        @if($landlordRequest->business_license_image)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-file-earmark-text"></i> Giấy phép kinh doanh</h6>
            </div>
            <div class="card-body">
                <img src="{{ \App\Helpers\ImageHelper::url($landlordRequest->business_license_image) }}" 
                     alt="Giấy phép kinh doanh" 
                     class="img-fluid rounded border" 
                     style="max-height: 400px; cursor: pointer;"
                     onclick="window.open(this.src, '_blank')">
            </div>
        </div>
        @endif

        @if($landlordRequest->rejection_reason)
        <div class="card border-danger mb-4">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle"></i> Lý do từ chối
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-line;">{{ $landlordRequest->rejection_reason }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin xử lý</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Ngày gửi</small>
                    <strong>{{ \App\Helpers\DateTimeHelper::formatDateTime($landlordRequest->created_at) }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Ngày xử lý</small>
                    <strong>
                        {{ $landlordRequest->approved_at ? \App\Helpers\DateTimeHelper::formatDateTime($landlordRequest->approved_at) : '—' }}
                    </strong>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Người xử lý</small>
                    <strong>{{ $landlordRequest->approver->name ?? '—' }}</strong>
                </div>
            </div>
        </div>

        @if($landlordRequest->isPending())
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="bi bi-tools"></i> Thao tác</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <form action="{{ route('admin.landlord-requests.approve', $landlordRequest) }}" method="POST" onsubmit="return confirm('Xác nhận duyệt yêu cầu này và tạo tài khoản chủ trọ?');">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Duyệt yêu cầu
                        </button>
                    </form>
                    <form action="{{ route('admin.landlord-requests.reject', $landlordRequest) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Lý do từ chối</label>
                            <textarea name="rejection_reason" rows="3" class="form-control @error('rejection_reason') is-invalid @enderror" placeholder="Nhập lý do từ chối" required>{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Xác nhận từ chối yêu cầu này?');">
                            <i class="bi bi-x-circle"></i> Từ chối yêu cầu
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <a href="{{ route('admin.landlord-requests.index') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>
@endsection

