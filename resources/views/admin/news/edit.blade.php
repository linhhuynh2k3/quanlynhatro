@extends('layouts.admin')

@section('page-title', 'Sửa tin tức')


@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-pencil"></i> Sửa tin tức</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Tiêu đề <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title', $news->title) }}" 
                           required 
                           class="form-control @error('title') is-invalid @enderror">
                    @error('title')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Slug
                    </label>
                    <input type="text" 
                           name="slug" 
                           value="{{ old('slug', $news->slug) }}" 
                           class="form-control @error('slug') is-invalid @enderror">
                    @error('slug')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Hình ảnh mới (để trống nếu không đổi)
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

                @if($news->thumbnail)
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Ảnh hiện tại
                    </label>
                    <div>
                        <img src="{{ \App\Helpers\ImageHelper::url($news->thumbnail) }}" alt="{{ $news->title }}" class="img-thumbnail" style="max-width: 400px;">
                    </div>
                </div>
                @endif

                <div class="col-12">
                    <label class="form-label fw-bold">
                         Tóm tắt
                    </label>
                    <textarea name="excerpt" 
                              rows="3" 
                              class="form-control">{{ old('excerpt', $news->excerpt) }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">
                         Nội dung <span class="text-danger">*</span>
                    </label>
                    <textarea name="content" 
                              rows="15" 
                              required 
                              class="form-control @error('content') is-invalid @enderror">{{ old('content', $news->content) }}</textarea>
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
                               {{ old('is_published', $news->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">
                             Xuất bản
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Cập nhật
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
