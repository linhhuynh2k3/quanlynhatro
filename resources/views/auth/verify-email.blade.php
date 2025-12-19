<x-guest-layout>
    @section('auth-title', 'Xác thực email')
    
    <div class="alert alert-info mb-4">
        
        Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác thực địa chỉ email của bạn bằng cách click vào link chúng tôi vừa gửi. Nếu bạn không nhận được email, chúng tôi sẽ gửi lại cho bạn.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
             
            Link xác thực mới đã được gửi đến địa chỉ email bạn đã cung cấp.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-grid gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-auth text-white w-100">
                 Gửi lại email xác thực
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">
                 Đăng xuất
            </button>
        </form>
    </div>
</x-guest-layout>
