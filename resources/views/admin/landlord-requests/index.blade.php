@extends('layouts.admin')

@section('page-title', 'Yêu cầu đăng ký chủ trọ')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Yêu cầu đăng ký chủ trọ</h5>
            <small class="text-muted">Quản lý các yêu cầu trở thành chủ trọ từ người dùng</small>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại dashboard
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('admin.landlord-requests.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Lọc
                    </button>
                </div>
                @if(request()->filled('status'))
                <div class="col-md-2">
                    <a href="{{ route('admin.landlord-requests.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle"></i> Xóa lọc
                    </a>
                </div>
                @endif
            </div>
        </form>

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

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ứng viên</th>
                        <th>Liên hệ</th>
                        <th>Tin nhắn</th>
                        <th>Trạng thái</th>
                        <th>Ngày gửi</th>
                        <th>Người xử lý</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td><strong>#{{ $request->id }}</strong></td>
                        <td>
                            <div class="fw-semibold">{{ $request->name }}</div>
                            <small class="text-muted">Email: {{ $request->email }}</small>
                        </td>
                        <td>
                            <span class="d-block"><i class="bi bi-telephone text-muted"></i> {{ $request->phone }}</span>
                            <small class="text-muted">{{ $request->address }}</small>
                        </td>
                        <td>
                            <span class="text-muted">
                                {{ \Illuminate\Support\Str::limit($request->message ?? '—', 60) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusClasses[$request->status] ?? 'secondary' }}">
                                {{ $statusLabels[$request->status] ?? ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>{{ \App\Helpers\DateTimeHelper::formatDateTime($request->created_at) }}</td>
                        <td>
                            @if($request->approver)
                                <span class="fw-semibold">{{ $request->approver->name }}</span>
                                <small class="d-block text-muted">{{ $request->approved_at ? \App\Helpers\DateTimeHelper::formatDateTime($request->approved_at) : '' }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.landlord-requests.show', $request) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                                @if($request->isPending())
                                <form action="{{ route('admin.landlord-requests.approve', $request) }}" method="POST" onsubmit="return confirm('Xác nhận duyệt yêu cầu này?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có yêu cầu nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $requests->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

