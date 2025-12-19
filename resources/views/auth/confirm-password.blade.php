<x-guest-layout>
    @section('auth-title', 'Xác nhận mật khẩu')
    
    <div class="alert alert-warning mb-4">
        Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu của bạn để tiếp tục.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-bold">Mật khẩu</label>
            <div class="input-group">
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       required 
                       autocomplete="current-password"
                       placeholder="Nhập mật khẩu của bạn">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'togglePasswordIcon')">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-auth text-white">Xác nhận</button>
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
</x-guest-layout>
