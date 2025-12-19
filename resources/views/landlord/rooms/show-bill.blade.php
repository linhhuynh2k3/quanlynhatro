@extends('layouts.landlord')

@section('page-title', 'Chi tiết hóa đơn')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="bi bi-receipt"></i> Chi tiết hóa đơn
            </h2>
            <p class="text-muted">Số hóa đơn: <strong>{{ $bill->bill_number }}</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông tin hóa đơn</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Người thuê:</strong><br>
                            {{ $bill->tenant->name }}<br>
                            <small class="text-muted">{{ $bill->tenant->email }}</small>
                        </div>
                        <div class="col-md-6">
                            <strong>Phòng trọ:</strong><br>
                            {{ $bill->listing->title }}<br>
                            <small class="text-muted">{{ $bill->listing->address }}</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Ngày lập:</strong> {{ $bill->bill_date->format('d/m/Y') }}
                        </div>
                        <div class="col-md-4">
                            <strong>Hạn thanh toán:</strong> {{ $bill->due_date->format('d/m/Y') }}
                        </div>
                        <div class="col-md-4">
                            <strong>Trạng thái:</strong>
                            @if($bill->payment_status === 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @else
                                <span class="badge bg-warning">Chưa thanh toán</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">Chi tiết hóa đơn</h6>
                    <table class="table table-bordered">
                        <tr>
                            <td>Tiền phòng:</td>
                            <td class="text-end">{{ number_format($bill->room_price, 0, ',', '.') }}₫</td>
                        </tr>
                        @if($bill->electricity_usage)
                        <tr>
                            <td>
                                Tiền điện ({{ $bill->electricity_usage }} kWh × {{ number_format($bill->electricity_price_per_unit, 0, ',', '.') }}₫):
                            </td>
                            <td class="text-end">{{ number_format($bill->electricity_total, 0, ',', '.') }}₫</td>
                        </tr>
                        @endif
                        @if($bill->water_usage)
                        <tr>
                            <td>
                                Tiền nước ({{ $bill->water_usage }} m³ × {{ number_format($bill->water_price_per_unit, 0, ',', '.') }}₫):
                            </td>
                            <td class="text-end">{{ number_format($bill->water_total, 0, ',', '.') }}₫</td>
                        </tr>
                        @endif
                        @if($bill->wifi_price > 0)
                        <tr>
                            <td>Tiền Wifi:</td>
                            <td class="text-end">{{ number_format($bill->wifi_price, 0, ',', '.') }}₫</td>
                        </tr>
                        @endif
                        @if($bill->garbage_price > 0)
                        <tr>
                            <td>Tiền rác:</td>
                            <td class="text-end">{{ number_format($bill->garbage_price, 0, ',', '.') }}₫</td>
                        </tr>
                        @endif
                        <tr class="table-success">
                            <td><strong>Tổng cộng:</strong></td>
                            <td class="text-end"><strong>{{ number_format($bill->total_amount, 0, ',', '.') }}₫</strong></td>
                        </tr>
                    </table>

                    @if($bill->notes)
                    <div class="mt-3">
                        <strong>Ghi chú:</strong>
                        <p class="text-muted">{{ $bill->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('landlord.rooms.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

