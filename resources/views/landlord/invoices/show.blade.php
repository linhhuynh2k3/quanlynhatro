@extends('layouts.landlord')

@section('page-title', 'Chi tiết hóa đơn')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Hóa đơn #{{ $invoice->invoice_number }}</h5>
                <div>
                    <span class="badge 
                        @if($invoice->status == 'paid') bg-success
                        @elseif($invoice->status == 'overdue') bg-danger
                        @else bg-warning
                        @endif">
                        @if($invoice->status == 'paid')
                            Đã thanh toán
                        @elseif($invoice->status == 'overdue')
                            Quá hạn
                        @else
                            Chờ thanh toán
                        @endif
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Invoice Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Thông tin chủ trọ</h6>
                        <p class="mb-1"><strong>Tên:</strong> {{ $invoice->landlord->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $invoice->landlord->email }}</p>
                        <p class="mb-0"><strong>Điện thoại:</strong> {{ $invoice->contract->listing->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Thông tin người thuê</h6>
                        <p class="mb-1"><strong>Tên:</strong> {{ $invoice->tenant->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $invoice->tenant->email }}</p>
                        <p class="mb-0"><strong>Địa chỉ:</strong> {{ $invoice->contract->listing->address ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Ngày lập:</strong> {{ \App\Helpers\DateTimeHelper::formatDate($invoice->invoice_date) }}</p>
                        <p><strong>Hạn thanh toán:</strong> {{ \App\Helpers\DateTimeHelper::formatDate($invoice->due_date) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Kỳ thanh toán:</strong> {{ \App\Helpers\DateTimeHelper::formatDate($invoice->period_start) }} - {{ \App\Helpers\DateTimeHelper::formatDate($invoice->period_end) }}</p>
                        <p><strong>Phòng trọ:</strong> {{ $invoice->contract->listing->title ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Nội dung</th>
                                <th>Đơn vị</th>
                                <th>Số cũ</th>
                                <th>Số mới</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Tiền trọ -->
                            <tr>
                                <td>1</td>
                                <td><strong>Tiền trọ</strong></td>
                                <td>Tháng</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="text-end"><strong>{{ number_format($invoice->rent_amount) }} VNĐ</strong></td>
                            </tr>

                            <!-- Điện -->
                            @if($invoice->electricity_new_reading)
                            <tr>
                                <td>2</td>
                                <td><strong>Điện</strong></td>
                                <td>kWh</td>
                                <td>{{ number_format($invoice->electricity_old_reading) }}</td>
                                <td>{{ number_format($invoice->electricity_new_reading) }}</td>
                                <td>{{ number_format($invoice->electricity_unit_price) }} VNĐ/kWh</td>
                                <td class="text-end"><strong>{{ number_format($invoice->electricity_amount) }} VNĐ</strong></td>
                            </tr>
                            @endif

                            <!-- Nước -->
                            @if($invoice->water_new_reading)
                            <tr>
                                <td>3</td>
                                <td><strong>Nước</strong></td>
                                <td>m³</td>
                                <td>{{ number_format($invoice->water_old_reading) }}</td>
                                <td>{{ number_format($invoice->water_new_reading) }}</td>
                                <td>{{ number_format($invoice->water_unit_price) }} VNĐ/m³</td>
                                <td class="text-end"><strong>{{ number_format($invoice->water_amount) }} VNĐ</strong></td>
                            </tr>
                            @endif

                            <!-- Wifi -->
                            @if($invoice->wifi_amount > 0)
                            <tr>
                                <td>4</td>
                                <td><strong>Wifi</strong></td>
                                <td>Tháng</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="text-end"><strong>{{ number_format($invoice->wifi_amount) }} VNĐ</strong></td>
                            </tr>
                            @endif

                            <!-- Rác -->
                            @if($invoice->trash_amount > 0)
                            <tr>
                                <td>5</td>
                                <td><strong>Rác</strong></td>
                                <td>Tháng</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="text-end"><strong>{{ number_format($invoice->trash_amount) }} VNĐ</strong></td>
                            </tr>
                            @endif

                            <!-- Dịch vụ khác -->
                            @if($invoice->other_services_amount > 0)
                            <tr>
                                <td>6</td>
                                <td><strong>Dịch vụ khác</strong><br><small class="text-muted">{{ $invoice->other_services_note }}</small></td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="text-end"><strong>{{ number_format($invoice->other_services_amount) }} VNĐ</strong></td>
                            </tr>
                            @endif

                            <!-- Tổng cộng -->
                            <tr class="table-primary">
                                <td colspan="6" class="text-end"><strong>TỔNG CỘNG:</strong></td>
                                <td class="text-end"><strong class="fs-5">{{ number_format($invoice->total_amount) }} VNĐ</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($invoice->notes)
                <div class="alert alert-info">
                    <strong>Ghi chú:</strong> {{ $invoice->notes }}
                </div>
                @endif

                @if($invoice->paid_at)
                <div class="alert alert-success">
                    <strong>Đã thanh toán vào:</strong> {{ \App\Helpers\DateTimeHelper::formatDateTime($invoice->paid_at) }}
                </div>
                @endif

                <div class="d-flex gap-2">
                    <a href="{{ route('landlord.invoices.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    @if($invoice->status != 'paid')
                    <a href="{{ route('landlord.invoices.edit', $invoice->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    @endif
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="bi bi-printer"></i> In hóa đơn
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

