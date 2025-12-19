@extends('layouts.landlord')

@section('page-title', 'Tạo hóa đơn tiền phòng')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="bi bi-receipt"></i> Tạo hóa đơn tiền phòng
            </h2>
            <p class="text-muted">Tính tiền phòng cho: <strong>{{ $contract->tenant->name }}</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông tin hợp đồng</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Phòng trọ:</strong><br>
                            {{ $contract->listing->title }}<br>
                            <small class="text-muted">{{ $contract->listing->address }}</small>
                        </div>
                        <div class="col-md-6">
                            <strong>Người thuê:</strong><br>
                            {{ $contract->tenant->name }}<br>
                            <small class="text-muted">{{ $contract->tenant->email }}</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Giá thuê/tháng:</strong> {{ number_format($contract->monthly_price, 0, ',', '.') }}₫
                        </div>
                        <div class="col-md-6">
                            <strong>Giá điện:</strong> {{ $contract->listing->electricity_price ? number_format($contract->listing->electricity_price, 0, ',', '.') . '₫/kWh' : 'Chưa cấu hình' }}<br>
                            <strong>Giá nước:</strong> {{ $contract->listing->water_price ? number_format($contract->listing->water_price, 0, ',', '.') . '₫/m³' : 'Chưa cấu hình' }}
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('landlord.rooms.store-bill', $contract->id) }}" method="POST" id="billForm">
                @csrf
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Thông tin hóa đơn</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ngày lập hóa đơn <span class="text-danger">*</span></label>
                                <input type="date" name="bill_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hạn thanh toán <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                            </div>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3">Tiền phòng</h6>
                        <div class="mb-3">
                            <label class="form-label">Tiền phòng (VNĐ/tháng) <span class="text-danger">*</span></label>
                            <input type="number" name="room_price" id="room_price" class="form-control" 
                                   value="{{ $contract->monthly_price }}" min="0" step="1000" required>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3">Tiền điện</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Số điện cũ</label>
                                <input type="number" name="electricity_old_reading" id="electricity_old_reading" 
                                       class="form-control" value="{{ $lastBill->electricity_new_reading ?? '' }}" 
                                       min="0" onchange="calculateElectricity()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số điện mới <span class="text-danger">*</span></label>
                                <input type="number" name="electricity_new_reading" id="electricity_new_reading" 
                                       class="form-control" min="0" onchange="calculateElectricity()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số điện tiêu thụ</label>
                                <input type="text" id="electricity_usage" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá điện/đơn vị</label>
                            <input type="text" id="electricity_price_display" class="form-control" 
                                   value="{{ $contract->listing->electricity_price ? number_format($contract->listing->electricity_price, 0, ',', '.') . '₫/kWh' : 'Chưa cấu hình' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tổng tiền điện</label>
                            <input type="text" id="electricity_total_display" class="form-control" value="0₫" readonly>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3">Tiền nước</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Số nước cũ</label>
                                <input type="number" name="water_old_reading" id="water_old_reading" 
                                       class="form-control" value="{{ $lastBill->water_new_reading ?? '' }}" 
                                       min="0" onchange="calculateWater()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số nước mới <span class="text-danger">*</span></label>
                                <input type="number" name="water_new_reading" id="water_new_reading" 
                                       class="form-control" min="0" onchange="calculateWater()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số nước tiêu thụ</label>
                                <input type="text" id="water_usage" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá nước/đơn vị</label>
                            <input type="text" id="water_price_display" class="form-control" 
                                   value="{{ $contract->listing->water_price ? number_format($contract->listing->water_price, 0, ',', '.') . '₫/m³' : 'Chưa cấu hình' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tổng tiền nước</label>
                            <input type="text" id="water_total_display" class="form-control" value="0₫" readonly>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3">Dịch vụ</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tiền Wifi (VNĐ/tháng)</label>
                                <input type="number" name="wifi_price" id="wifi_price" class="form-control" 
                                       value="{{ $contract->listing->wifi_price ?? 0 }}" min="0" step="1000" onchange="calculateTotal()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tiền rác (VNĐ/tháng)</label>
                                <input type="number" name="garbage_price" id="garbage_price" class="form-control" 
                                       value="{{ $contract->listing->garbage_price ?? 0 }}" min="0" step="1000" onchange="calculateTotal()">
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Nhập ghi chú nếu có..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Tổng tiền</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td>Tiền phòng:</td>
                                        <td class="text-end" id="room_price_display">0₫</td>
                                    </tr>
                                    <tr>
                                        <td>Tiền điện:</td>
                                        <td class="text-end" id="electricity_total_row">0₫</td>
                                    </tr>
                                    <tr>
                                        <td>Tiền nước:</td>
                                        <td class="text-end" id="water_total_row">0₫</td>
                                    </tr>
                                    <tr>
                                        <td>Tiền Wifi:</td>
                                        <td class="text-end" id="wifi_price_display">0₫</td>
                                    </tr>
                                    <tr>
                                        <td>Tiền rác:</td>
                                        <td class="text-end" id="garbage_price_display">0₫</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h3 class="text-success mb-0" id="total_amount_display">0₫</h3>
                                    <p class="text-muted">Tổng cộng</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-send"></i> Gửi hóa đơn cho người thuê
                    </button>
                    <a href="{{ route('landlord.rooms.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
const electricityPrice = {{ $contract->listing->electricity_price ?? 0 }};
const waterPrice = {{ $contract->listing->water_price ?? 0 }};

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
}

function calculateElectricity() {
    const oldReading = parseInt(document.getElementById('electricity_old_reading').value) || 0;
    const newReading = parseInt(document.getElementById('electricity_new_reading').value) || 0;
    const usage = Math.max(0, newReading - oldReading);
    
    document.getElementById('electricity_usage').value = usage + ' kWh';
    
    const total = usage * electricityPrice;
    document.getElementById('electricity_total_display').value = formatCurrency(total);
    document.getElementById('electricity_total_row').textContent = formatCurrency(total);
    
    calculateTotal();
}

function calculateWater() {
    const oldReading = parseInt(document.getElementById('water_old_reading').value) || 0;
    const newReading = parseInt(document.getElementById('water_new_reading').value) || 0;
    const usage = Math.max(0, newReading - oldReading);
    
    document.getElementById('water_usage').value = usage + ' m³';
    
    const total = usage * waterPrice;
    document.getElementById('water_total_display').value = formatCurrency(total);
    document.getElementById('water_total_row').textContent = formatCurrency(total);
    
    calculateTotal();
}

function calculateTotal() {
    const roomPrice = parseFloat(document.getElementById('room_price').value) || 0;
    const electricityTotal = (parseInt(document.getElementById('electricity_new_reading').value) || 0) - (parseInt(document.getElementById('electricity_old_reading').value) || 0);
    const electricityAmount = Math.max(0, electricityTotal) * electricityPrice;
    
    const waterTotal = (parseInt(document.getElementById('water_new_reading').value) || 0) - (parseInt(document.getElementById('water_old_reading').value) || 0);
    const waterAmount = Math.max(0, waterTotal) * waterPrice;
    
    const wifiPrice = parseFloat(document.getElementById('wifi_price').value) || 0;
    const garbagePrice = parseFloat(document.getElementById('garbage_price').value) || 0;
    
    const total = roomPrice + electricityAmount + waterAmount + wifiPrice + garbagePrice;
    
    document.getElementById('room_price_display').textContent = formatCurrency(roomPrice);
    document.getElementById('wifi_price_display').textContent = formatCurrency(wifiPrice);
    document.getElementById('garbage_price_display').textContent = formatCurrency(garbagePrice);
    document.getElementById('total_amount_display').textContent = formatCurrency(total);
}

// Tính toán ban đầu
calculateTotal();

// Auto calculate khi thay đổi
document.getElementById('room_price').addEventListener('input', calculateTotal);
</script>
@endsection
@endsection

