@extends('layouts.frontend')

@section('title', 'Quản lý đặt thuê - Homestay.com')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
             Quản lý đặt thuê của tôi
        </h2>
        <a href="{{ route('listings.index') }}" class="btn btn-primary">
             Tìm phòng mới
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Bài đăng</th>
                            <th>Chủ trọ</th>
                            <th>Giá thuê</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Phê duyệt</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                        <tr>
                            <td>
                                <a href="{{ route('listings.show', $contract->listing->id) }}" class="text-decoration-none">
                                    <strong>{{ Str::limit($contract->listing->title, 40) }}</strong>
                                </a>
                            </td>
                            <td>
                                 {{ $contract->landlord->name }}
                            </td>
                            <td>
                                <strong class="text-primary">{{ number_format($contract->monthly_price) }} VNĐ/tháng</strong>
                            </td>
                            <td>
                                 {{ \App\Helpers\DateTimeHelper::formatDate($contract->start_date) }}
                            </td>
                            <td>
                                 {{ \App\Helpers\DateTimeHelper::formatDate($contract->end_date) }}
                            </td>
                            <td>
                                @if($contract->approval_status === 'approved')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Đã duyệt
                                    </span>
                                @elseif($contract->approval_status === 'rejected')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i> Bị từ chối
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split me-1"></i> Chờ duyệt
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($contract->payment_status === 'paid')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Đã thanh toán
                                    </span>
                                @elseif($contract->payment_status === 'processing')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split me-1"></i> Đang xử lý
                                    </span>
                                @elseif($contract->payment_status === 'failed')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i> Thất bại
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-cash-coin me-1"></i> Chưa thanh toán
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($contract->status == 'active')
                                    <span class="badge bg-success">
                                         Đang hoạt động
                                    </span>
                                @elseif($contract->status == 'completed')
                                    <span class="badge bg-info">
                                         Đã hoàn thành
                                    </span>
                                @elseif($contract->status == 'cancelled')
                                    <span class="badge bg-danger">
                                         Đã hủy
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                         Đang chờ
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    @if($contract->approval_status === 'pending')
                                        <span class="text-muted small">Đang chờ chủ trọ duyệt.</span>
                                    @elseif($contract->approval_status === 'rejected')
                                        <span class="text-danger small">Đơn đã bị từ chối.</span>
                                    @else
                                        @if($contract->payment_status === 'unpaid')
                                            <form action="{{ route('contracts.pay', $contract->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Thanh toán hợp đồng này qua VNPAY?')">
                                                    <i class="bi bi-credit-card"></i> Thanh toán VNPAY
                                                </button>
                                            </form>
                                        @elseif($contract->payment_status === 'processing')
                                            <span class="text-warning small">Đang chờ xác nhận thanh toán...</span>
                                        @elseif($contract->payment_status === 'paid' && $contract->status !== 'active')
                                            <button type="button"
                                                class="btn btn-outline-success btn-sm w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#signContractModal"
                                                data-action="{{ route('contracts.sign', $contract->id) }}"
                                                data-listing="{{ $contract->listing->title }}"
                                                data-landlord="{{ $contract->landlord->name }}"
                                                data-start="{{ \App\Helpers\DateTimeHelper::formatDate($contract->start_date) }}"
                                                data-end="{{ \App\Helpers\DateTimeHelper::formatDate($contract->end_date) }}"
                                                data-price="{{ number_format($contract->monthly_price) }} VNĐ/tháng">
                                                <i class="bi bi-pen"></i> Ký hợp đồng
                                            </button>
                                        @endif
                                    @endif
                                    <a href="{{ route('listings.show', $contract->listing->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                         Xem bài đăng
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                
                                <p class="text-muted mb-3">Bạn chưa có đặt thuê nào</p>
                                <a href="{{ route('listings.index') }}" class="btn btn-primary">
                                     Tìm kiếm bài đăng
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($contracts->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $contracts->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Sign Contract Modal -->
<div class="modal fade" id="signContractModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ký hợp đồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="signContractForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3" id="contractSummary"></p>
                    <div class="mb-3">
                        <label class="form-label">Chữ ký điện tử <span class="text-danger">*</span></label>
                        <input type="text" name="signature_name" class="form-control" placeholder="Nhập họ tên của bạn" required>
                        <small class="text-muted">Chữ ký này sẽ được lưu cùng hợp đồng.</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeTermsInput" name="agree_terms" value="1" required>
                        <label class="form-check-label" for="agreeTermsInput">
                            Tôi xác nhận đã đọc và đồng ý với các điều khoản thuê phòng.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Ký hợp đồng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const signContractModal = document.getElementById('signContractModal');
    if (!signContractModal) return;

    signContractModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const action = button.getAttribute('data-action');
        const listing = button.getAttribute('data-listing');
        const landlord = button.getAttribute('data-landlord');
        const start = button.getAttribute('data-start');
        const end = button.getAttribute('data-end');
        const price = button.getAttribute('data-price');

        const form = document.getElementById('signContractForm');
        form.action = action;
        form.reset();

        const summary = `
            Bạn đang ký hợp đồng thuê cho <strong>${listing}</strong><br>
            Chủ trọ: <strong>${landlord}</strong><br>
            Thời gian thuê: <strong>${start}</strong> - <strong>${end}</strong><br>
            Giá thuê: <strong>${price}</strong>
        `;
        document.getElementById('contractSummary').innerHTML = summary;
    });
});
</script>
@endsection
