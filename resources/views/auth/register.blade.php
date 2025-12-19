<x-guest-layout>
    @section('auth-title', 'Đăng ký tài khoản người tìm phòng')
    
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Họ và tên</label>
            <input id="name" 
                   type="text" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Nhập họ và tên">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-bold">Email</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   placeholder="Nhập email của bạn">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-bold">Mật khẩu</label>
            <div class="input-group">
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       required 
                       autocomplete="new-password"
                       placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)">
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
            <button type="submit" class="btn btn-auth text-white">Đăng ký</button>
        </div>

        <div class="text-center">
            <p class="mb-0 text-muted">Đã có tài khoản?</p>
            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mt-2">
                Đăng nhập ngay
            </a>
        </div>

        <hr class="my-3">

        <div class="text-center">
            <p class="mb-0 text-muted small">Bạn muốn đăng ký làm chủ trọ?</p>
            <a href="{{ route('landlord.register') }}" class="btn btn-outline-success w-100 mt-2">
                Đăng ký chủ trọ
            </a>
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
