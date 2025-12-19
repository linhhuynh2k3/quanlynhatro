@extends('layouts.admin')

@section('page-title', 'Sửa danh mục')


@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-pencil"></i> Sửa danh mục</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Tên danh mục <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $category->name) }}" 
                           required 
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')
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
                           value="{{ old('slug', $category->slug) }}" 
                           class="form-control @error('slug') is-invalid @enderror">
                    @error('slug')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Danh mục cha
                    </label>
                    <select name="parent_id" class="form-select">
                        <option value="">Không có (danh mục cha)</option>
                        @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">
                         Vị trí
                    </label>
                    <input type="number" 
                           name="position" 
                           value="{{ old('position', $category->position) }}" 
                           class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold d-block">&nbsp;</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="is_active" 
                               value="1" 
                               id="is_active"
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                             Hoạt động
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">
                         Mô tả
                    </label>
                    <textarea name="description" 
                              rows="3" 
                              class="form-control">{{ old('description', $category->description ?? '') }}</textarea>
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
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
@endsection
