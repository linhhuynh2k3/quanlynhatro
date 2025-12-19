@extends('layouts.frontend')

@section('title', $listing->title . ' - Homestay.com')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('listings.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($listing->title, 30) }}</li>
                </ol>
            </nav>

            <!-- Images Gallery -->
            <div class="detail-gallery mb-4">
                @php
                    $images = json_decode($listing->images ?? '[]', true);
                @endphp
                @if(!empty($images))
                <div id="imageGallery" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($images as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ \App\Helpers\ImageHelper::url($image) }}" class="d-block w-100" alt="{{ $listing->title }}">
                        </div>
                        @endforeach
                    </div>
                    @if(count($images) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageGallery" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageGallery" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    @endif
                    <div class="carousel-indicators">
                        @foreach($images as $index => $image)
                        <button type="button" data-bs-target="#imageGallery" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
                    <div class="text-center text-muted">
                        <p class="mt-2">Chưa có hình ảnh</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Listing Details -->
            <div class="detail-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="flex-grow-1">
                        <h1 class="detail-title">{{ $listing->title }}</h1>
                        <div class="d-flex gap-2 mb-3">
                            @if($listing->is_featured)
                            <span class="badge bg-danger">Nổi bật</span>
                            @endif
                            @if($listing->created_at->diffInDays(now()) <= 3)
                            <span class="badge bg-success">Mới đăng</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="price-highlight">{{ number_format($listing->price) }} VNĐ</div>
                        <small class="text-muted">/tháng</small>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="detail-info-grid">
                    <div class="detail-info-item">
                        <label>Diện tích</label>
                        <div class="value">{{ $listing->area }} m²</div>
                    </div>
                    <div class="detail-info-item">
                        <label>Địa chỉ</label>
                        <div class="value">{{ Str::limit($listing->address, 20) }}</div>
                    </div>
                    <div class="detail-info-item">
                        <label>Quận/Huyện</label>
                        <div class="value">{{ $listing->district }}</div>
                    </div>
                    <div class="detail-info-item">
                        <label>Tỉnh/Thành</label>
                        <div class="value">{{ $listing->province }}</div>
                    </div>
                <div class="detail-info-item">
                    <label>Phòng còn</label>
                    <div class="value {{ $listing->available_units > 0 ? 'text-success' : 'text-danger' }}">
                        {{ $listing->available_units }} / {{ $listing->total_units }}
                    </div>
                </div>
                </div>

                <!-- Description -->
                <div class="mt-4">
                    <h3 class="mb-3">Mô tả chi tiết</h3>
                    <div class="text-muted" style="white-space: pre-line; line-height: 1.8;">{{ $listing->description }}</div>
                </div>

                <!-- Meta Info -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                    <div>
                        <small class="text-muted">
                            Đăng bởi: <strong>{{ $listing->user->name }}</strong>
                        </small>
                        <br>
                        <small class="text-muted">
                            {{ \App\Helpers\DateTimeHelper::diffForHumans($listing->created_at) }}
                        </small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            Lượt xem: <strong>{{ $listing->views }}</strong>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="detail-card">
                <h3 class="mb-4">
                    Bình luận 
                    <span class="badge bg-primary">{{ $listing->comments->count() }}</span>
                </h3>
                
                <!-- Comment Form -->
                @auth
                <form action="{{ route('comments.store', $listing->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <textarea name="content" rows="4" class="form-control" placeholder="Viết bình luận của bạn..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi bình luận</button>
                </form>
                @else
                <div class="alert alert-info">
                    Vui lòng <a href="{{ route('login') }}" class="alert-link">đăng nhập</a> để bình luận.
                </div>
                @endauth

                <!-- Comments List -->
                <div class="mt-4">
                    @forelse($listing->comments->where('parent_id', null) as $comment)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $comment->user->name }}</h6>
                                    <small class="text-muted">
                                        {{ \App\Helpers\DateTimeHelper::diffForHumans($comment->created_at) }}
                                    </small>
                                </div>
                                @auth
                                @if($comment->user_id === auth()->id())
                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                                @endif
                                @endauth
                            </div>
                            <p class="mb-2">{{ $comment->content }}</p>
                            
                            <!-- Reply Form -->
                            @auth
                            <button class="btn btn-sm btn-outline-primary" onclick="toggleReply({{ $comment->id }})">
                                Trả lời
                            </button>
                            <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.reply', $comment->id) }}" method="POST" class="mt-2 d-none">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="content" rows="2" class="form-control" placeholder="Viết phản hồi..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">Gửi</button>
                            </form>
                            @endauth

                            <!-- Replies -->
                            @foreach($comment->replies as $reply)
                            <div class="card mt-2 ms-4 bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 small">{{ $reply->user->name }}</h6>
                                            <small class="text-muted">
                                                {{ \App\Helpers\DateTimeHelper::diffForHumans($reply->created_at) }}
                                            </small>
                                        </div>
                                        @auth
                                        @if($reply->user_id === auth()->id())
                                        <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                        @endif
                                        @endauth
                                    </div>
                                    <p class="mb-0 small">{{ $reply->content }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <p class="mt-2">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Contact Card -->
            <div class="sidebar-card sticky-top" style="top: 100px;">
                <h5 class="sidebar-title">Liên hệ</h5>
                <div class="mb-3">
                    <p class="mb-2">
                        <strong>Chủ trọ:</strong><br>
                        {{ $listing->user->name }}
                    </p>
                    <p class="mb-2">
                        <strong>Điện thoại:</strong><br>
                        @auth
                        <a href="tel:{{ $listing->phone }}" class="text-decoration-none">
                            <strong class="text-primary">{{ $listing->phone }}</strong>
                        </a>
                        @else
                        <span class="text-muted">Vui lòng đăng nhập để xem</span>
                        @endauth
                    </p>
                </div>
                
                @auth
                    @if(auth()->user()->isTenant())
                        @if($listing->available_units > 0)
                        <button onclick="showBookingModal()" class="btn btn-danger w-100 mb-2">
                            Đặt thuê ngay
                        </button>
                        @else
                        <div class="alert alert-warning">
                            Bài đăng hiện đã hết phòng. Vui lòng theo dõi sau hoặc liên hệ chủ trọ.
                        </div>
                        @endif
                        <a href="{{ route('chat.start', $listing->id) }}" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-chat-dots"></i> Chat với chủ trọ
                        </a>
                    @endif
                @else
                <a href="{{ route('login') }}" class="btn btn-danger w-100 mb-2">
                    Đăng nhập để liên hệ
                </a>
                @if($listing->available_units < 1)
                <div class="alert alert-warning">
                    Bài đăng hiện đã hết phòng. Vui lòng quay lại sau.
                </div>
                @endif
                @endauth
                
                <button class="btn btn-outline-primary w-100" onclick="shareListing()">
                    Chia sẻ
                </button>
            </div>

            <!-- Related Listings -->
            @if(isset($related_listings) && $related_listings->count() > 0)
            <div class="sidebar-card">
                <h5 class="sidebar-title">Bài đăng liên quan</h5>
                <div class="featured-listings">
                    @foreach($related_listings->take(5) as $related)
                    <div class="featured-item">
                        @php
                            $images = json_decode($related->images ?? '[]', true);
                            $firstImage = !empty($images) ? $images[0] : 'default-listing.jpg';
                        @endphp
                        <img src="{{ \App\Helpers\ImageHelper::url($firstImage) }}" alt="{{ $related->title }}">
                        <div class="featured-item-content">
                            <h5>
                                <a href="{{ route('listings.show', $related->id) }}">
                                    {{ Str::limit($related->title, 50) }}
                                </a>
                            </h5>
                            <p class="price mb-0">{{ number_format($related->price) }} VNĐ</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Booking Modal -->
@auth
@if(auth()->user()->isTenant())
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Đặt thuê</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('bookings.store', $listing->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thời gian thuê <span class="text-danger">*</span></label>
                        <select name="rental_period" id="rental_period" class="form-select" required>
                            <option value="">-- Chọn thời gian thuê --</option>
                            <option value="3">3 tháng</option>
                            <option value="6">6 tháng</option>
                            <option value="9">9 tháng</option>
                            <option value="12">1 năm (12 tháng)</option>
                        </select>
                        <input type="hidden" name="end_date" id="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú (tùy chọn)</label>
                        <textarea name="note" rows="3" class="form-control" placeholder="Nhập ghi chú nếu có..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Xác nhận đặt thuê</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endauth

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const rentalPeriodSelect = document.getElementById('rental_period');
    const endDateInput = document.getElementById('end_date');
    
    function calculateEndDate() {
        const startDate = startDateInput.value;
        const rentalPeriod = rentalPeriodSelect.value;
        
        if (startDate && rentalPeriod) {
            const start = new Date(startDate);
            const months = parseInt(rentalPeriod);
            const end = new Date(start);
            end.setMonth(end.getMonth() + months);
            
            // Format date as YYYY-MM-DD
            const year = end.getFullYear();
            const month = String(end.getMonth() + 1).padStart(2, '0');
            const day = String(end.getDate()).padStart(2, '0');
            endDateInput.value = `${year}-${month}-${day}`;
        } else {
            endDateInput.value = '';
        }
    }
    
    startDateInput.addEventListener('change', calculateEndDate);
    rentalPeriodSelect.addEventListener('change', calculateEndDate);
});
</script>
<script>
function toggleReply(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    form.classList.toggle('d-none');
}

function showBookingModal() {
    const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
    modal.show();
}

function shareListing() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $listing->title }}',
            text: '{{ Str::limit($listing->description, 100) }}',
            url: window.location.href
        });
    } else {
        // Fallback: Copy to clipboard
        navigator.clipboard.writeText(window.location.href);
        alert('Đã sao chép link vào clipboard!');
    }
}
</script>
@endsection
