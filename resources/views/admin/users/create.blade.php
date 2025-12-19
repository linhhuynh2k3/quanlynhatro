@extends('layouts.admin')

@section('page-title', 'Thêm người dùng mới')


@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-person-plus"></i> Thêm người dùng mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Họ và tên <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Nhập họ và tên">
                    @error('name')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="Nhập email">
                    @error('email')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Mật khẩu <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               name="password" 
                               required 
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Tối thiểu 8 ký tự">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="bi bi-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Xác nhận mật khẩu <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               name="password_confirmation" 
                               required 
                               class="form-control"
                               placeholder="Nhập lại mật khẩu">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                            <i class="bi bi-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Vai trò <span class="text-danger">*</span>
                    </label>
                    <select name="role" 
                            required 
                            class="form-select @error('role') is-invalid @enderror">
                        <option value="">Chọn vai trò</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Tác nhân (Quản trị viên)</option>
                        <option value="landlord" {{ old('role') == 'landlord' ? 'selected' : '' }}>Chủ trọ</option>
                        <option value="tenant" {{ old('role') == 'tenant' ? 'selected' : '' }}>Người tìm phòng</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Tạo người dùng
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId, iconId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}
</script>
@endsection
