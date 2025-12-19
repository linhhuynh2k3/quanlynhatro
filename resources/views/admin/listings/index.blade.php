@extends('layouts.admin')

@section('page-title', 'Quản lý bài đăng')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-post"></i> Quản lý bài đăng</h5>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('admin.listings.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm kiếm theo tiêu đề, địa chỉ...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                </div>
            </div>
        </form>

        <!-- Listings Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Chủ trọ</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listings as $listing)
                    <tr>
                        <td><strong>#{{ $listing->id }}</strong></td>
                        <td>
                            <div class="fw-bold">{{ Str::limit($listing->title, 40) }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ Str::limit($listing->address, 30) }}</small>
                        </td>
                        <td><i class="bi bi-person"></i> {{ $listing->user->name }}</td>
                        <td>
                            <strong class="text-primary"><i class="bi bi-currency-dollar"></i> {{ number_format($listing->price) }} VNĐ</strong>
                        </td>
                        <td>
                            @if($listing->status == 'approved')
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã duyệt</span>
                            @elseif($listing->status == 'pending')
                                <span class="badge bg-warning"><i class="bi bi-clock-history"></i> Chờ duyệt</span>
                            @elseif($listing->status == 'rejected')
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Từ chối</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($listing->status) }}</span>
                            @endif
                        </td>
                        <td><i class="bi bi-calendar3"></i> {{ \App\Helpers\DateTimeHelper::formatDate($listing->created_at) }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.listings.show', $listing->id) }}" class="btn btn-outline-primary" title="Xem">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($listing->status == 'pending')
                                <form action="{{ route('admin.listings.approve', $listing->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Duyệt" onclick="return confirm('Xác nhận duyệt bài đăng này?')">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.listings.reject', $listing->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger" title="Từ chối" onclick="return confirm('Xác nhận từ chối bài đăng này?')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endif
                                @if($listing->status == 'approved')
                                <form action="{{ route('admin.listings.expire', $listing->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning" title="Đánh dấu hết hạn" onclick="return confirm('Xác nhận đánh dấu hết hạn?')">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Không có bài đăng nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($listings->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $listings->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
