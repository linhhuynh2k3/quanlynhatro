@extends('layouts.admin')

@section('page-title', 'Thêm tin tức mới')


@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-newspaper"></i> Thêm tin tức mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Tiêu đề <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title') }}" 
                           required 
                           class="form-control @error('title') is-invalid @enderror"
                           placeholder="Nhập tiêu đề tin tức">
                    @error('title')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Slug (tự động tạo nếu để trống)
                    </label>
                    <input type="text" 
                           name="slug" 
                           value="{{ old('slug') }}" 
                           class="form-control @error('slug') is-invalid @enderror"
                           placeholder="VD: tin-tuc-moi">
                    @error('slug')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Hình ảnh đại diện
                    </label>
                    <input type="file" 
                           name="image" 
                           accept="image/*" 
                           class="form-control @error('image') is-invalid @enderror"
                           onchange="previewImage(this)">
                    @error('image')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                    <div id="imagePreview" class="mt-2"></div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">
                         Tóm tắt
                    </label>
                    <textarea name="excerpt" 
                              rows="3" 
                              class="form-control"
                              placeholder="Nhập tóm tắt ngắn gọn...">{{ old('excerpt') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">
                         Nội dung <span class="text-danger">*</span>
                    </label>
                    <textarea name="content" 
                              rows="15" 
                              required 
                              class="form-control @error('content') is-invalid @enderror"
                              placeholder="Nhập nội dung tin tức...">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="is_published" 
                               value="1" 
                               id="is_published"
                               {{ old('is_published', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">
                             Xuất bản ngay
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Tạo tin tức
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-thumbnail';
            img.style.maxWidth = '300px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
