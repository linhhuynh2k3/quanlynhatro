@extends('layouts.frontend')

@section('title', $news->title . ' - Homestay.com')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <article class="card shadow-sm">
                @if($news->thumbnail)
                <img src="{{ \App\Helpers\ImageHelper::url($news->thumbnail) }}" 
                     alt="{{ $news->title }}" 
                     class="card-img-top" 
                     style="max-height: 400px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="bi bi-calendar3"></i> {{ \App\Helpers\DateTimeHelper::formatDateTime($news->published_at) }}
                            <span class="ms-3">
                                <i class="bi bi-person"></i> {{ $news->author->name ?? 'Admin' }}
                            </span>
                        </small>
                    </div>
                    <h1 class="card-title mb-4">{{ $news->title }}</h1>
                    @if($news->excerpt)
                    <div class="lead text-muted mb-4">
                        {{ $news->excerpt }}
                    </div>
                    @endif
                    <div class="news-content">
                        {!! $news->content !!}
                    </div>
                </div>
            </article>

            <!-- Related News -->
            @if($relatedNews->count() > 0)
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-newspaper"></i> Tin tức liên quan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($relatedNews as $item)
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                @if($item->thumbnail)
                                <a href="{{ route('news.show', $item->slug) }}" class="flex-shrink-0">
                                    <img src="{{ \App\Helpers\ImageHelper::url($item->thumbnail) }}" 
                                         alt="{{ $item->title }}" 
                                         class="rounded" 
                                         style="width: 100px; height: 80px; object-fit: cover;">
                                </a>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none text-dark">
                                            {{ Str::limit($item->title, 50) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3"></i> {{ \App\Helpers\DateTimeHelper::formatDate($item->published_at) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Danh mục</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="{{ route('listings.index') }}" class="text-decoration-none">
                                <i class="bi bi-house"></i> Phòng trọ
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('listings.index', ['category_id' => 2]) }}" class="text-decoration-none">
                                <i class="bi bi-building"></i> Nhà nguyên căn
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('listings.index', ['category_id' => 3]) }}" class="text-decoration-none">
                                <i class="bi bi-building"></i> Căn hộ
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('listings.index', ['category_id' => 4]) }}" class="text-decoration-none">
                                <i class="bi bi-shop"></i> Mặt bằng
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-newspaper"></i> Tin mới nhất</h5>
                </div>
                <div class="card-body">
                    @php
                        $latestNews = \App\Models\News::with('author')
                            ->where('is_published', true)
                            ->where('id', '!=', $news->id)
                            ->whereNotNull('published_at')
                            ->where('published_at', '<=', now())
                            ->orderBy('published_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @if($latestNews->count() > 0)
                    <ul class="list-unstyled mb-0">
                        @foreach($latestNews as $item)
                        <li class="mb-3 pb-3 border-bottom">
                            <h6 class="mb-1">
                                <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($item->title, 50) }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-calendar3"></i> {{ \App\Helpers\DateTimeHelper::formatDate($item->published_at) }}
                            </small>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted mb-0">Chưa có tin tức nào</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.news-content {
    line-height: 1.8;
    font-size: 1.1rem;
}

.news-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 20px 0;
}

.news-content p {
    margin-bottom: 1.5rem;
}

.news-content h2,
.news-content h3,
.news-content h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}
</style>
@endsection

