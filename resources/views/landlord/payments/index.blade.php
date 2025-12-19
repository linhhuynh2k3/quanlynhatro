@extends('layouts.landlord')

@section('page-title', 'Nạp tiền')


@section('content')
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);">
            <div class="card-body text-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-white-50 mb-0">Số dư ví</h6>
                    <i class="bi bi-wallet2 fs-3"></i>
                </div>
                <h2 class="mb-3 fw-bold">{{ number_format($balance) }} VNĐ</h2>
                <p class="mb-0 text-white-50 small">Sử dụng để thanh toán phí đăng bài và gia hạn.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-primary me-3">
                    <i class="bi bi-arrow-down-up"></i>
                </div>
                <div>
                    <h6 class="mb-1">Lịch sử giao dịch</h6>
                    <p class="mb-0 text-muted small">Theo dõi mọi lần nạp tiền & trừ phí</p>
                </div>
            </div>
            <a href="{{ route('landlord.payments.history') }}" class="btn btn-outline-primary w-100">
                <i class="bi bi-clock-history me-1"></i> Xem ngay
            </a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-info me-3">
                    <i class="bi bi-badge-ad"></i>
                </div>
                <div>
                    <h6 class="mb-1">Quản lý bài đăng</h6>
                    <p class="mb-0 text-muted small">Thanh toán phí đăng bài để hiển thị</p>
                </div>
            </div>
            <a href="{{ route('landlord.listings.index') }}" class="btn btn-outline-info w-100">
                <i class="bi bi-layout-text-window-reverse me-1"></i> Tới danh sách
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Nạp tiền qua VNPAY</h5>
            <small class="text-muted">Thanh toán an toàn qua cổng VNPAY trong vài bước</small>
        </div>
        <img src="https://sandbox.vnpayment.vn/apis/assets/img/logo.png" alt="VNPAY" style="height:32px;">
    </div>
    <div class="card-body">
        <form action="{{ route('landlord.payments.deposit') }}" method="POST" class="row g-4">
            @csrf
            <div class="col-md-6">
                <label class="form-label fw-semibold">Số tiền (VNĐ) <span class="text-danger">*</span></label>
                <input type="number"
                       name="amount"
                       min="10000"
                       step="1000"
                       required
                       class="form-control form-control-lg @error('amount') is-invalid @enderror"
                       placeholder="Ví dụ: 200000"
                       value="{{ old('amount') }}">
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-outline-success flex-fill" onclick="setAmount(100000)">100k</button>
                    <button type="button" class="btn btn-outline-success flex-fill" onclick="setAmount(500000)">500k</button>
                    <button type="button" class="btn btn-outline-success flex-fill" onclick="setAmount(1000000)">1 triệu</button>
                </div>
                <small class="text-muted d-block mt-2">Số tiền tối thiểu 10,000 VNĐ</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Kênh thanh toán</label>
                <div class="form-control form-control-lg bg-light">
                    <i class="bi bi-credit-card-2-front me-1"></i> VNPAY (mặc định)
                </div>
                <input type="hidden" name="bank_code" value="">
                <label class="form-label fw-semibold mt-4">Ghi chú</label>
                <textarea name="description"
                          rows="3"
                          class="form-control"
                          placeholder="Ví dụ: Nạp tiền để đăng bài trong tháng này">{{ old('description') }}</textarea>
            </div>
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-shield-lock me-2"></i> Bạn sẽ được chuyển tới trang VNPAY để thanh toán.
                    Số dư sẽ cập nhật tự động sau khi giao dịch thành công.
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success btn-lg w-100">
                    <i class="bi bi-lightning-charge me-1"></i> Thanh toán qua VNPAY
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function setAmount(amount) {
    document.querySelector('input[name="amount"]').value = amount;
}
</script>
@endsection
