@extends('layouts.admin')

@section('page-title', 'Thống kê doanh thu')


@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Tổng nạp tiền</h6>
                    <h3 class="mb-0 text-success">{{ number_format($stats['total_deposits']) }} VNĐ</h3>
                </div>
                <div class="stat-icon bg-success">
                    <i class="bi bi-wallet2 text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Tổng thanh toán</h6>
                    <h3 class="mb-0 text-primary">{{ number_format($stats['total_listing_payments']) }} VNĐ</h3>
                </div>
                <div class="stat-icon bg-primary">
                    <i class="bi bi-credit-card text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-info border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Doanh thu hôm nay</h6>
                    <h3 class="mb-0 text-info">{{ number_format($stats['today_revenue']) }} VNĐ</h3>
                </div>
                <div class="stat-icon bg-info">
                    <i class="bi bi-calendar-day text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-warning border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Doanh thu tháng này</h6>
                    <h3 class="mb-0 text-warning">{{ number_format($stats['month_revenue']) }} VNĐ</h3>
                </div>
                <div class="stat-icon bg-warning">
                    <i class="bi bi-calendar-month text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-graph-up"></i> Biểu đồ doanh thu 30 ngày gần nhất</h5>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-end justify-content-between gap-2" style="height: 300px;">
            @php
                $maxAmount = $chartData->max('total') ?? 1;
            @endphp
            @foreach($chartData as $data)
            <div class="flex-fill d-flex flex-column align-items-center">
                <div class="w-100 bg-primary rounded-top" 
                     style="height: {{ ($data->total / $maxAmount) * 100 }}%; min-height: 5px;" 
                     title="{{ number_format($data->total) }} VNĐ"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top">
                </div>
                <small class="text-muted mt-2" style="writing-mode: vertical-rl; text-orientation: mixed; transform: rotate(180deg);">
                    {{ \App\Helpers\DateTimeHelper::format($data->date, 'd/m') }}
                </small>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<script>
// Initialize Bootstrap tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
@endsection
