@extends('layouts.frontend')

@section('title', 'Dashboard - Người thuê')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold"><i class="bi bi-house-door"></i> Dashboard - Quản lý phòng đã thuê</h2>
            <p class="text-muted">Quản lý các phòng trọ bạn đã đặt thuê và trò chuyện với chủ trọ</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Tổng hợp đồng</h6>
                            <h3 class="mb-0 text-primary">{{ $stats['total_contracts'] }}</h3>
                        </div>
                        <div class="text-primary" style="font-size: 2.5rem;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Đang thuê</h6>
                            <h3 class="mb-0 text-success">{{ $stats['active_contracts'] }}</h3>
                        </div>
                        <div class="text-success" style="font-size: 2.5rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Chờ duyệt</h6>
                            <h3 class="mb-0 text-warning">{{ $stats['pending_contracts'] }}</h3>
                        </div>
                        <div class="text-warning" style="font-size: 2.5rem;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Tin nhắn chưa đọc</h6>
                            <h3 class="mb-0 text-info">{{ $stats['unread_messages'] }}</h3>
                        </div>
                        <div class="text-info" style="font-size: 2.5rem;">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Contracts List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Phòng đã thuê</h5>
                </div>
                <div class="card-body">
                    @forelse($contracts as $contract)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1">
                                    <a href="{{ route('listings.show', $contract->listing_id) }}" class="text-decoration-none">
                                        {{ $contract->listing->title ?? 'N/A' }}
                                    </a>
                                </h6>
                                <p class="text-muted mb-1 small">
                                    <i class="bi bi-geo-alt"></i> {{ $contract->listing->address ?? 'N/A' }}
                                </p>
                                <p class="text-muted mb-1 small">
                                    <i class="bi bi-person"></i> Chủ trọ: {{ $contract->landlord->name }}
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge 
                                    @if($contract->status == 'active') bg-success
                                    @elseif($contract->approval_status == 'pending') bg-warning
                                    @else bg-secondary
                                    @endif">
                                    @if($contract->status == 'active')
                                        Đang thuê
                                    @elseif($contract->approval_status == 'pending')
                                        Chờ duyệt
                                    @else
                                        {{ ucfirst($contract->status) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <small class="text-muted">Giá thuê:</small>
                                <div class="fw-bold text-primary">{{ number_format($contract->monthly_price) }} VNĐ/tháng</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Ngày bắt đầu:</small>
                                <div>{{ \App\Helpers\DateTimeHelper::formatDate($contract->start_date) }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Ngày kết thúc:</small>
                                <div>{{ \App\Helpers\DateTimeHelper::formatDate($contract->end_date) }}</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('listings.show', $contract->listing_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Xem chi tiết
                            </a>
                            <a href="{{ route('chat.start', $contract->listing_id) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-chat"></i> Chat với chủ trọ
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        <p>Bạn chưa có phòng nào đã thuê.</p>
                        <a href="{{ route('listings.index') }}" class="btn btn-primary">Tìm phòng ngay</a>
                    </div>
                    @endforelse

                    @if($contracts->hasPages())
                    <div class="mt-3">
                        {{ $contracts->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Conversations Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Cuộc trò chuyện</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($conversations as $conversation)
                    <a href="{{ route('chat.show', $conversation->id) }}" class="text-decoration-none text-dark">
                        <div class="border-bottom p-3 hover-bg-light">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold">
                                        {{ $conversation->landlord->name }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $conversation->listing->title ?? 'N/A' }}
                                    </small>
                                </div>
                                @if($conversation->unread_count > 0)
                                <span class="badge bg-danger">{{ $conversation->unread_count }}</span>
                                @endif
                            </div>
                            @if($conversation->messages->count() > 0)
                            <p class="mb-0 small text-muted">
                                {{ Str::limit($conversation->messages->first()->message, 50) }}
                            </p>
                            @endif
                        </div>
                    </a>
                    @empty
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-chat fs-1 d-block mb-2"></i>
                        <p class="mb-0">Chưa có cuộc trò chuyện nào</p>
                    </div>
                    @endforelse
                    <div class="p-3 border-top">
                        <a href="{{ route('chat.index') }}" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-arrow-right"></i> Xem tất cả
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}
</style>
@endsection

