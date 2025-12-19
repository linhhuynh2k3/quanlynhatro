@extends('layouts.admin')

@section('page-title', 'Quản lý tin tức')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-newspaper"></i> Quản lý tin tức</h5>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm tin tức
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('admin.news.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           class="form-control" 
                           placeholder="Tìm kiếm theo tiêu đề, nội dung...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                </div>
            </div>
        </form>

        <!-- News Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $item)
                    <tr>
                        <td>
                            @if($item->thumbnail)
                            <img src="{{ \App\Helpers\ImageHelper::url($item->thumbnail) }}" alt="{{ $item->title }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ Str::limit($item->title, 50) }}</strong>
                        </td>
                        <td>
                            <i class="bi bi-person"></i> {{ $item->author->name ?? 'N/A' }}
                        </td>
                        <td>
                            @if($item->is_published)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Đã xuất bản
                            </span>
                            @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-file-earmark"></i> Bản nháp
                            </span>
                            @endif
                        </td>
                        <td><i class="bi bi-calendar3"></i> {{ \App\Helpers\DateTimeHelper::formatDate($item->created_at) }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.news.show', $item->id) }}" class="btn btn-outline-primary" title="Xem">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-outline-success" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa tin tức này?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có tin tức nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($news->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $news->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
