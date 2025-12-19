@extends('layouts.admin')

@section('page-title', 'Chi tiết tin tức')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
    <div>
        <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil-square"></i> Chỉnh sửa
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h3 class="mb-2">{{ $news->title }}</h3>
                <div class="text-muted">
                    <span><i class="bi bi-tag"></i> {{ $news->slug }}</span>
                    <span class="mx-2">•</span>
                    <span><i class="bi bi-clock-history"></i> {{ \App\Helpers\DateTimeHelper::formatDateTime($news->created_at) }}</span>
                </div>
            </div>
            <div>
                <span class="badge {{ $news->is_published ? 'bg-success' : 'bg-secondary' }}">
                    {{ $news->is_published ? 'Đã xuất bản' : 'Nháp' }}
                </span>
            </div>
        </div>

        @if($news->thumbnail)
            <div class="mb-4">
                <img src="{{ \App\Helpers\ImageHelper::url($news->thumbnail) }}" class="img-fluid rounded" alt="{{ $news->title }}">
            </div>
        @endif

        @if($news->excerpt)
            <div class="alert alert-info">
                <strong>Tóm tắt:</strong> {{ $news->excerpt }}
            </div>
        @endif

        <div class="content">
            {!! nl2br(e($news->content)) !!}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-circle"></i> Thông tin biên tập</h5>
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Tác giả</dt>
            <dd class="col-sm-9">{{ $news->author->name ?? 'N/A' }}</dd>

            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9">{{ $news->author->email ?? '—' }}</dd>

            <dt class="col-sm-3">Ngày cập nhật</dt>
            <dd class="col-sm-9">{{ \App\Helpers\DateTimeHelper::formatDateTime($news->updated_at) }}</dd>
        </dl>
    </div>
</div>
@endsection

