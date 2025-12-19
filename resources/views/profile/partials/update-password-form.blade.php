<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="update_password_current_password" class="form-label fw-bold">Mật khẩu hiện tại</label>
        <div class="input-group">
            <input id="update_password_current_password" 
                   name="current_password" 
                   type="password" 
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                   autocomplete="current-password">
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_current_password', 'togglePasswordIcon1')">
                <i class="bi bi-eye" id="togglePasswordIcon1"></i>
            </button>
        </div>
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password" class="form-label fw-bold">Mật khẩu mới</label>
        <div class="input-group">
            <input id="update_password_password" 
                   name="password" 
                   type="password" 
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                   autocomplete="new-password">
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password', 'togglePasswordIcon2')">
                <i class="bi bi-eye" id="togglePasswordIcon2"></i>
            </button>
        </div>
        @error('password', 'updatePassword')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password_confirmation" class="form-label fw-bold">Xác nhận mật khẩu mới</label>
        <div class="input-group">
            <input id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   class="form-control" 
                   autocomplete="new-password">
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password_confirmation', 'togglePasswordIcon3')">
                <i class="bi bi-eye" id="togglePasswordIcon3"></i>
            </button>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-warning">Cập nhật mật khẩu</button>

        @if (session('status') === 'password-updated')
            <div class="alert alert-success mb-0 py-2" role="alert">
                Mật khẩu đã được cập nhật!
            </div>
        @endif
    </div>
</form>

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
