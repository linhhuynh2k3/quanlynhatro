@extends('layouts.landlord')

@section('page-title', 'Quản lý bài đăng')


@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"> Quản lý bài đăng</h5>
        <a href="{{ route('landlord.listings.create') }}" class="btn btn-success">
             Đăng bài mới
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('landlord.listings.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-10">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                         Lọc
                    </button>
                </div>
            </div>
        </form>

        <!-- Listings -->
        <div class="row g-4">
            @forelse($listings as $listing)
            <div class="col-12">
                <div class="card border-start border-4 
                    @if($listing->status == 'approved') border-success
                    @elseif($listing->status == 'pending') border-warning
                    @elseif($listing->status == 'rejected') border-danger
                    @else border-secondary
                    @endif">
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Image -->
                            <div class="col-md-3">
                                @php
                                    $images = json_decode($listing->images ?? '[]', true);
                                    $firstImage = !empty($images) ? $images[0] : null;
                                @endphp
                                @if($firstImage)
                                <img src="{{ \App\Helpers\ImageHelper::url($firstImage) }}" alt="{{ $listing->title }}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                                @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                    
                                </div>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="col-md-9">
                                <div class="d-flex justify-content-between align-items-start mb-2">
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
                                
                                <p class="text-muted mb-3">{{ Str::limit($listing->description, 120) }}</p>
                                
                                <div class="row g-2 mb-3">
                                    <div class="col-auto">
                                        <span class="badge bg-primary">
                                             {{ number_format($listing->price) }} VNĐ/tháng
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-info">
                                             {{ $listing->area }} m²
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-secondary">
                                             {{ $listing->district }}, {{ $listing->province }}
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-light text-dark">
                                            Còn {{ $listing->available_units }}/{{ $listing->total_units }} phòng
                                        </span>
                                    </div>
                                </div>
                                
                                @if($listing->expired_at)
                                <div class="mb-3">
                                    <small class="text-muted">
                                         Hết hạn: 
                                        <strong class="{{ $listing->expired_at->isPast() ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Helpers\DateTimeHelper::formatDate($listing->expired_at) }}
                                        </strong>
                                        @if($listing->expired_at->isPast())
                                        <span class="badge bg-danger ms-2">Đã hết hạn</span>
                                        @endif
                                    </small>
                                </div>
                                @endif
                                
                                <!-- Actions -->
                                <div class="btn-group" role="group">
                                    <a href="{{ route('landlord.listings.show', $listing->id) }}" class="btn btn-sm btn-outline-primary">
                                         Xem
                                    </a>
                                    @if($listing->status != 'approved' || ($listing->expired_at && $listing->expired_at->isPast()))
                                    <a href="{{ route('landlord.listings.edit', $listing->id) }}" class="btn btn-sm btn-outline-warning">
                                         Sửa
                                    </a>
                                    @endif
                                    @if($listing->status == 'approved' && $listing->expired_at && $listing->expired_at->isFuture())
                                    <form action="{{ route('landlord.listings.extend', $listing->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="days" value="30">
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Gia hạn bài đăng thêm 30 ngày?')">
                                             Gia hạn
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('landlord.listings.destroy', $listing->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa bài đăng này?')">
                                             Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    
                    <h5 class="text-muted mb-3">Chưa có bài đăng nào</h5>
                    <a href="{{ route('landlord.listings.create') }}" class="btn btn-success">
                         Đăng bài đầu tiên
                    </a>
                </div>
            </div>
            @endforelse
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
