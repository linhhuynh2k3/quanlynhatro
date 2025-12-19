@extends('layouts.admin')

@section('page-title', 'Chi tiết bài đăng')


@section('content')
<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $listing->title }}</h5>
                @if($listing->status == 'approved')
                    <span class="badge bg-success">Đã duyệt</span>
                @elseif($listing->status == 'pending')
                    <span class="badge bg-warning">Chờ duyệt</span>
                @elseif($listing->status == 'rejected')
                    <span class="badge bg-danger">Từ chối</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($listing->status) }}</span>
                @endif
            </div>
            <div class="card-body">
                <!-- Images -->
                @php
                    $images = json_decode($listing->images ?? '[]', true);
                @endphp
                @if(!empty($images))
                <div class="row g-2 mb-4">
                    @foreach($images as $image)
                    <div class="col-md-4">
                        <img src="{{ \App\Helpers\ImageHelper::url($image) }}" alt="{{ $listing->title }}" class="img-fluid rounded" style="height: 200px; width: 100%; object-fit: cover;">
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Details -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="border-start border-primary border-4 ps-3">
                            <small class="text-muted d-block">Giá</small>
                            <h4 class="text-primary mb-0">{{ number_format($listing->price) }} VNĐ/tháng</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border-start border-info border-4 ps-3">
                            <small class="text-muted d-block">Diện tích</small>
                            <h4 class="text-info mb-0">{{ $listing->area }} m²</h4>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border-start border-success border-4 ps-3">
                            <small class="text-muted d-block">Địa chỉ</small>
                            <p class="mb-0">
                                 
                                {{ $listing->address }}, {{ $listing->ward }}, {{ $listing->district }}, {{ $listing->province }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Số điện thoại</small>
                        <p class="mb-0"> {{ $listing->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Danh mục</small>
                        <p class="mb-0"> {{ $listing->category->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <h6 class="fw-bold mb-3">Mô tả</h6>
                    <div class="text-muted" style="white-space: pre-line;">{{ $listing->description }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Owner Info -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"> Chủ trọ</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Tên:</strong> {{ $listing->user->name }}</p>
                <p class="mb-2"><strong>Email:</strong> {{ $listing->user->email }}</p>
                <p class="mb-0"><strong>Số dư:</strong> <span class="text-success">{{ number_format($listing->user->balance) }} VNĐ</span></p>
            </div>
        </div>

        <!-- Actions -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"> Thao tác</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($listing->status == 'pending')
                    <form action="{{ route('admin.listings.approve', $listing->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Xác nhận duyệt bài đăng này?')">
                             Duyệt bài
                        </button>
                    </form>
                    <form action="{{ route('admin.listings.reject', $listing->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Xác nhận từ chối bài đăng này?')">
                             Từ chối
                        </button>
                    </form>
                    @endif
                    @if($listing->status == 'approved')
                    <form action="{{ route('admin.listings.expire', $listing->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Xác nhận đánh dấu hết hạn?')">
                             Đánh dấu hết hạn
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('admin.listings.index') }}" class="btn btn-secondary w-100">
                         Quay lại
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"> Thống kê</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Lượt xem</small>
                    <strong>{{ $listing->views }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Ngày đăng</small>
                    <strong>{{ \App\Helpers\DateTimeHelper::formatDateTime($listing->created_at) }}</strong>
                </div>
                @if($listing->approved_at)
                <div class="mb-2">
                    <small class="text-muted d-block">Ngày duyệt</small>
                    <strong>{{ \App\Helpers\DateTimeHelper::formatDateTime($listing->approved_at) }}</strong>
                </div>
                @endif
                @if($listing->expired_at)
                <div class="mb-0">
                    <small class="text-muted d-block">Hết hạn</small>
                    <strong class="{{ $listing->expired_at->isPast() ? 'text-danger' : 'text-success' }}">
                        {{ \App\Helpers\DateTimeHelper::formatDateTime($listing->expired_at) }}
                    </strong>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
