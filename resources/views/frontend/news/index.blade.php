@extends('layouts.frontend')

@section('title', 'Tin tức - Homestay.com')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="hero-title">Tin tức về phòng trọ</h1>
                <p class="hero-subtitle">Cập nhật những thông tin mới nhất về thị trường bất động sản, phòng trọ, nhà trọ</p>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <!-- Search Box -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('news.index') }}" class="row g-3">
                <div class="col-md-10">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           class="form-control form-control-lg" 
                           placeholder="Tìm kiếm tin tức...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- News Grid -->
    @if($news->count() > 0)
    <div class="row g-4">
        @foreach($news as $item)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                @if($item->thumbnail)
                <a href="{{ route('news.show', $item->slug) }}">
                    <img src="{{ \App\Helpers\ImageHelper::url($item->thumbnail) }}" 
                         alt="{{ $item->title }}" 
                         class="card-img-top" 
                         style="height: 200px; object-fit: cover;">
                </a>
                @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="bi bi-image text-muted fs-1"></i>
                </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="bi bi-calendar3"></i> {{ \App\Helpers\DateTimeHelper::formatDate($item->published_at) }}
                            <span class="ms-2">
                                <i class="bi bi-person"></i> {{ $item->author->name ?? 'Admin' }}
                            </span>
                        </small>
                    </div>
                    <h5 class="card-title">
                        <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none text-dark">
                            {{ Str::limit($item->title, 60) }}
                        </a>
                    </h5>
                    @if($item->excerpt)
                    <p class="card-text text-muted flex-grow-1">
                        {{ Str::limit($item->excerpt, 100) }}
                    </p>
                    @endif
                    <a href="{{ route('news.show', $item->slug) }}" class="btn btn-outline-primary mt-auto">
                        Đọc thêm <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($news->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $news->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
    @else
    <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
        <h4 class="text-muted">Không tìm thấy tin tức nào</h4>
        <p class="text-muted">Vui lòng thử lại với từ khóa khác</p>
    </div>
    @endif
</div>
@endsection

