@extends('layouts.admin')

@section('page-title', 'Chi tiết danh mục')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning">
        <i class="bi bi-pencil-square"></i> Chỉnh sửa
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Thông tin danh mục</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Tên</dt>
                    <dd class="col-sm-8">{{ $category->name }}</dd>

                    <dt class="col-sm-4">Slug</dt>
                    <dd class="col-sm-8"><span class="badge bg-light text-dark">{{ $category->slug }}</span></dd>

                    <dt class="col-sm-4">Mô tả</dt>
                    <dd class="col-sm-8">{{ $category->description ?? '—' }}</dd>

                    <dt class="col-sm-4">Danh mục cha</dt>
                    <dd class="col-sm-8">
                        @if($category->parent)
                            <a href="{{ route('admin.categories.show', $category->parent->id) }}">
                                {{ $category->parent->name }}
                            </a>
                        @else
                            <span class="text-muted">Không có</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Trạng thái</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $category->is_active ? 'Đang hoạt động' : 'Tạm ẩn' }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Vị trí</dt>
                    <dd class="col-sm-8">{{ $category->position ?? '—' }}</dd>

                    <dt class="col-sm-4">Ngày tạo</dt>
                    <dd class="col-sm-8">{{ \App\Helpers\DateTimeHelper::formatDateTime($category->created_at) }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Danh mục con</h5>
                <span class="badge bg-primary">{{ $category->children->count() }} danh mục</span>
            </div>
            <div class="card-body">
                @forelse($category->children as $child)
                    <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                        <div>
                            <strong>{{ $child->name }}</strong>
                            <div class="text-muted small">{{ $child->slug }}</div>
                        </div>
                        <a href="{{ route('admin.categories.show', $child->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                @empty
                    <p class="text-muted mb-0">Chưa có danh mục con.</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-building"></i> Bài đăng thuộc danh mục</h5>
                <span class="badge bg-info text-dark">{{ $category->listings->count() }} bài đăng</span>
            </div>
            <div class="card-body">
                @forelse($category->listings->take(5) as $listing)
                    <div class="border-bottom pb-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $listing->title }}</strong>
                            <span class="badge bg-light text-dark text-uppercase">{{ $listing->status }}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-person"></i> {{ $listing->user->name ?? '—' }} |
                            <i class="bi bi-clock-history"></i> {{ \App\Helpers\DateTimeHelper::formatDate($listing->created_at) }}
                        </small>
                    </div>
                @empty
                    <p class="text-muted mb-0">Chưa có bài đăng nào.</p>
                @endforelse
                @if($category->listings->count() > 5)
                    <small class="text-muted">... và {{ $category->listings->count() - 5 }} bài đăng khác.</small>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

