@extends('layouts.frontend')

@section('title', 'Hóa đơn tiền phòng - ' . $bill->bill_number)

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-receipt"></i> Hóa đơn tiền phòng
                    </h4>
                    <small>Số hóa đơn: <strong>{{ $bill->bill_number }}</strong></small>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Thông tin người thuê</h6>
                            <p class="mb-1"><strong>{{ $bill->tenant->name }}</strong></p>
                            <p class="mb-1 text-muted">{{ $bill->tenant->email }}</p>
                            <p class="mb-0 text-muted">{{ $bill->tenant->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Thông tin phòng trọ</h6>
                            <p class="mb-1"><strong>{{ $bill->listing->title }}</strong></p>
                            <p class="mb-0 text-muted">{{ $bill->listing->address }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h6 class="text-muted">Ngày lập</h6>
                            <p class="mb-0">{{ $bill->bill_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Hạn thanh toán</h6>
                            <p class="mb-0">{{ $bill->due_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Trạng thái</h6>
                            @if($bill->payment_status === 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @else
                                <span class="badge bg-warning">Chưa thanh toán</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">Chi tiết hóa đơn</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Tiền phòng:</strong></td>
                                <td class="text-end">{{ number_format($bill->room_price, 0, ',', '.') }}₫</td>
                            </tr>
                            @if($bill->electricity_usage)
                            <tr>
                                <td>
                                    <strong>Tiền điện:</strong><br>
                                    <small class="text-muted">
                                        Số cũ: {{ $bill->electricity_old_reading }} kWh<br>
                                        Số mới: {{ $bill->electricity_new_reading }} kWh<br>
                                        Tiêu thụ: {{ $bill->electricity_usage }} kWh × {{ number_format($bill->electricity_price_per_unit, 0, ',', '.') }}₫
                                    </small>
                                </td>
                                <td class="text-end">{{ number_format($bill->electricity_total, 0, ',', '.') }}₫</td>
                            </tr>
                            @endif
                            @if($bill->water_usage)
                            <tr>
                                <td>
                                    <strong>Tiền nước:</strong><br>
                                    <small class="text-muted">
                                        Số cũ: {{ $bill->water_old_reading }} m³<br>
                                        Số mới: {{ $bill->water_new_reading }} m³<br>
                                        Tiêu thụ: {{ $bill->water_usage }} m³ × {{ number_format($bill->water_price_per_unit, 0, ',', '.') }}₫
                                    </small>
                                </td>
                                <td class="text-end">{{ number_format($bill->water_total, 0, ',', '.') }}₫</td>
                            </tr>
                            @endif
                            @if($bill->wifi_price > 0)
                            <tr>
                                <td><strong>Tiền Wifi:</strong></td>
                                <td class="text-end">{{ number_format($bill->wifi_price, 0, ',', '.') }}₫</td>
                            </tr>
                            @endif
                            @if($bill->garbage_price > 0)
                            <tr>
                                <td><strong>Tiền rác:</strong></td>
                                <td class="text-end">{{ number_format($bill->garbage_price, 0, ',', '.') }}₫</td>
                            </tr>
                            @endif
                            <tr class="table-success">
                                <td><strong>Tổng cộng:</strong></td>
                                <td class="text-end">
                                    <h4 class="mb-0 text-success">{{ number_format($bill->total_amount, 0, ',', '.') }}₫</h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if($bill->notes)
                    <div class="alert alert-info">
                        <strong>Ghi chú:</strong> {{ $bill->notes }}
                    </div>
                    @endif

                    @if($bill->payment_status !== 'paid')
                    <div class="text-center mt-4">
                        <form action="{{ route('utility-bills.pay', $bill->id) }}" method="POST" id="paymentForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Chọn ngân hàng (tùy chọn)</label>
                                <select name="bank_code" class="form-select">
                                    <option value="">Tất cả ngân hàng</option>
                                    <option value="VNBANK">Ngân hàng VNBank</option>
                                    <option value="VIETCOMBANK">Ngân hàng Vietcombank</option>
                                    <option value="VIETINBANK">Ngân hàng VietinBank</option>
                                    <option value="BIDV">Ngân hàng BIDV</option>
                                    <option value="TECHCOMBANK">Ngân hàng Techcombank</option>
                                    <option value="ACB">Ngân hàng ACB</option>
                                    <option value="SACOMBANK">Ngân hàng Sacombank</option>
                                    <option value="VPBANK">Ngân hàng VPBank</option>
                                    <option value="TPBANK">Ngân hàng TPBank</option>
                                    <option value="MBBANK">Ngân hàng MB Bank</option>
                                </select>
                                <small class="text-muted">Nếu không chọn, bạn sẽ được chuyển đến trang chọn ngân hàng của VNPay</small>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-credit-card"></i> Thanh toán qua VNPay
                            </button>
                        </form>
                        <p class="text-muted mt-2">
                            <small>Bạn sẽ được chuyển đến trang thanh toán VNPay</small>
                        </p>
                    </div>
                    @else
                    <div class="alert alert-success text-center">
                        <i class="bi bi-check-circle"></i> Hóa đơn đã được thanh toán vào {{ $bill->paid_at->format('d/m/Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

