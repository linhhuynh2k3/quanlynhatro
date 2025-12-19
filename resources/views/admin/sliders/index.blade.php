@extends('layouts.admin')

@section('page-title', 'Quản lý Slider')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-images"></i> Quản lý Slider</h5>
        <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm slider
        </a>
    </div>
    <div class="card-body">
        <!-- Sliders Grid -->
        <div class="row g-4">
            @forelse($sliders as $slider)
            <div class="col-md-4">
                <div class="card h-100 border-start border-primary border-4">
                    <div class="position-relative">
                        @if($slider->image)
                        <img src="{{ \App\Helpers\ImageHelper::url($slider->image) }}" alt="{{ $slider->title }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image text-muted fs-1"></i>
                        </div>
                        @endif
                        @if($slider->is_active)
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">
                            <i class="bi bi-check-circle"></i> Hoạt động
                        </span>
                        @else
                        <span class="badge bg-secondary position-absolute top-0 end-0 m-2">
                            <i class="bi bi-pause-circle"></i> Tạm dừng
                        </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-image"></i> {{ $slider->title }}</h5>
                        @if($slider->description)
                        <p class="card-text text-muted small">{{ Str::limit($slider->description, 60) }}</p>
                        @endif
                        <p class="text-muted small mb-3">
                            <i class="bi bi-sort-numeric-down"></i> Vị trí: {{ $slider->position }}
                        </p>
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('admin.sliders.edit', $slider->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Sửa
                            </a>
                            <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa slider này?')">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-images fs-1 d-block mb-2"></i>
                    Chưa có slider nào. <a href="{{ route('admin.sliders.create') }}">Tạo slider đầu tiên!</a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
