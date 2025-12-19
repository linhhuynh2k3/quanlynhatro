@extends('layouts.landlord')

@section('page-title', 'Quản lý đơn đặt thuê')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0"><i class="bi bi-card-list me-2"></i> Đơn đặt thuê</h5>
            <small class="text-muted">Theo dõi các yêu cầu thuê mà người dùng đã gửi</small>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('landlord.bookings.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label small text-muted">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đã ký</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Thanh toán</label>
                <select name="payment_status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                    <option value="processing" {{ request('payment_status') == 'processing' ? 'selected' : '' }}>Đang thanh toán</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thanh toán lỗi</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Phê duyệt</label>
                <select name="approval_status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                </select>
            </div>
            <div class="col-12 d-flex align-items-end">
                <button class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Lọc
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Người thuê</th>
                        <th>Bài đăng</th>
                        <th>Thời gian thuê</th>
                        <th>Phê duyệt</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                    <tr>
                        <td>#{{ $contract->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $contract->tenant->name }}</div>
                            <small class="text-muted">{{ $contract->tenant->email }}</small>
                        </td>
                        <td>
                            <a href="{{ route('listings.show', $contract->listing_id) }}" target="_blank">
                                {{ Str::limit($contract->listing->title, 40) }}
                            </a>
                        </td>
                        <td>
                            <div><strong>{{ \App\Helpers\DateTimeHelper::formatDate($contract->start_date) }}</strong></div>
                            <small class="text-muted">đến {{ \App\Helpers\DateTimeHelper::formatDate($contract->end_date) }}</small>
                        </td>
                        <td>
                            <span class="badge
                                @if($contract->approval_status === 'approved') bg-success
                                @elseif($contract->approval_status === 'rejected') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ strtoupper($contract->approval_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge 
                                @if($contract->payment_status === 'paid') bg-success
                                @elseif($contract->payment_status === 'processing') bg-warning text-dark
                                @elseif($contract->payment_status === 'failed') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ strtoupper($contract->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge 
                                @if($contract->status === 'active') bg-success
                                @elseif($contract->status === 'pending') bg-warning text-dark
                                @elseif($contract->status === 'completed') bg-info
                                @elseif($contract->status === 'cancelled') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($contract->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('landlord.bookings.show', $contract) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($contract->approval_status === 'pending')
                                <form action="{{ route('landlord.bookings.approve', $contract) }}" method="POST" onsubmit="return confirm('Duyệt đơn này?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('landlord.bookings.reject', $contract) }}" method="POST" onsubmit="return confirm('Từ chối đơn này?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có đơn đặt thuê nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $contracts->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

