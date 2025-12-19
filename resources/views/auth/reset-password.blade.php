<x-guest-layout>
    @section('auth-title', 'Đặt lại mật khẩu')
    
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-bold">Email</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $request->email) }}" 
                   required 
                   autofocus 
                   autocomplete="username"
                   placeholder="Nhập email của bạn">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-bold">Mật khẩu mới</label>
            <div class="input-group">
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       required 
                       autocomplete="new-password"
                       placeholder="Nhập mật khẩu mới">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'togglePasswordIcon1')">
                    <i class="bi bi-eye" id="togglePasswordIcon1"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-bold">Xác nhận mật khẩu</label>
            <div class="input-group">
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       class="form-control" 
                       required 
                       autocomplete="new-password"
                       placeholder="Nhập lại mật khẩu">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', 'togglePasswordIcon2')">
                    <i class="bi bi-eye" id="togglePasswordIcon2"></i>
                </button>
            </div>
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-auth text-white">Đặt lại mật khẩu</button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-decoration-none">Quay lại đăng nhập</a>
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
