@extends('layouts.admin')

@section('page-title', 'Quản lý danh mục')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-folder"></i> Quản lý danh mục</h5>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm danh mục
        </a>
    </div>
    <div class="card-body">
        <!-- Categories Tree -->
        <div class="row g-3">
            @forelse($categories->whereNull('parent_id') as $category)
            <div class="col-12">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="mb-2">
                                    <i class="bi bi-folder-fill text-primary"></i> {{ $category->name }}
                                </h5>
                                @if($category->is_active)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Hoạt động
                                </span>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-pause-circle"></i> Tạm dừng
                                </span>
                                @endif
                                @if($category->position)
                                <span class="badge bg-info ms-2">
                                    <i class="bi bi-sort-numeric-down"></i> Vị trí: {{ $category->position }}
                                </span>
                                @endif
                            </div>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-sm btn-outline-primary" title="Xem">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-success" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        @if($category->children->count() > 0)
                        <div class="mt-3 ps-4 border-start border-2 border-light">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-folder2"></i> Danh mục con:
                            </h6>
                            @foreach($category->children as $child)
                            <div class="card mb-2 bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-folder2-open text-muted"></i>
                                            <strong>{{ $child->name }}</strong>
                                            @if($child->is_active)
                                            <span class="badge bg-success ms-2">
                                                <i class="bi bi-check-circle"></i> Hoạt động
                                            </span>
                                            @else
                                            <span class="badge bg-secondary ms-2">
                                                <i class="bi bi-pause-circle"></i> Tạm dừng
                                            </span>
                                            @endif
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.categories.edit', $child->id) }}" class="btn btn-outline-success" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $child->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                    Chưa có danh mục nào. <a href="{{ route('admin.categories.create') }}">Tạo danh mục đầu tiên!</a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
