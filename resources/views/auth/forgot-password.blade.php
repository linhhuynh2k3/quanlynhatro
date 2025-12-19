<x-guest-layout>
    @section('auth-title', 'Quên mật khẩu')
    
    <div class="alert alert-info mb-4">
         
        Quên mật khẩu? Không sao. Hãy nhập email của bạn và chúng tôi sẽ gửi link đặt lại mật khẩu.
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
             {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-bold">
                 Email
            </label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   placeholder="Nhập email của bạn">
            @error('email')
                <div class="invalid-feedback">
                     {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-auth text-white">
                 Gửi link đặt lại mật khẩu
            </button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-decoration-none">
                 Quay lại đăng nhập
            </a>
        </div>
    </form>
</x-guest-layout>
