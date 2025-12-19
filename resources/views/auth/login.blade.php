<x-guest-layout>
    @section('auth-title', 'Đăng nhập vào tài khoản')
    
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-bold">Email</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" 
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
            <label for="password" class="form-label fw-bold">Mật khẩu</label>
            <div class="input-group">
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       required 
                       autocomplete="current-password"
                       placeholder="Nhập mật khẩu">
                <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" onclick="togglePassword('password', 'togglePasswordIcon')">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input id="remember_me" 
                   type="checkbox" 
                   name="remember" 
                   class="form-check-input">
            <label class="form-check-label" for="remember_me">
                Ghi nhớ đăng nhập
            </label>
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-auth text-white">Đăng nhập</button>
        </div>

        <div class="text-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-decoration-none">
                    Quên mật khẩu?
                </a>
            @endif
        </div>

        <hr class="my-4">

        <div class="text-center">
            <p class="mb-0 text-muted">Chưa có tài khoản?</p>
            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mt-2">
                Đăng ký người tìm phòng
            </a>
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
