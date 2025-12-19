@extends('layouts.admin')

@section('page-title', 'Thêm slider mới')


@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-image"></i> Thêm slider mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
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
                           placeholder="Nhập tiêu đề slider">
                    @error('title')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">
                         Mô tả
                    </label>
                    <textarea name="description" 
                              rows="3" 
                              class="form-control"
                              placeholder="Nhập mô tả cho slider...">{{ old('description') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Hình ảnh <span class="text-danger">*</span>
                    </label>
                    <input type="file" 
                           name="image" 
                           accept="image/*" 
                           required 
                           class="form-control @error('image') is-invalid @enderror"
                           onchange="previewImage(this)">
                    @error('image')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                    <small class="text-muted">
                         JPG, PNG, GIF, WEBP, tối đa 2MB
                    </small>
                    <div id="imagePreview" class="mt-2"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Liên kết (URL)
                    </label>
                    <input type="url" 
                           name="link" 
                           value="{{ old('link') }}" 
                           class="form-control @error('link') is-invalid @enderror"
                           placeholder="https://...">
                    @error('link')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Vị trí
                    </label>
                    <input type="number" 
                           name="position" 
                           value="{{ old('position', 0) }}" 
                           class="form-control"
                           placeholder="0">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold d-block">&nbsp;</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="is_active" 
                               value="1" 
                               id="is_active"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                             Hoạt động
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Tạo slider
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
