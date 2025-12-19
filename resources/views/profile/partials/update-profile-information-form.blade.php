<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label fw-bold">
             Họ và tên
        </label>
        <input id="name" 
               name="name" 
               type="text" 
               class="form-control @error('name') is-invalid @enderror" 
               value="{{ old('name', $user->name) }}" 
               required 
               autofocus 
               autocomplete="name">
        @error('name')
            <div class="invalid-feedback">
                 {{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label fw-bold">
             Email
        </label>
        <input id="email" 
               name="email" 
               type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               value="{{ old('email', $user->email) }}" 
               required 
               autocomplete="username">
        @error('email')
            <div class="invalid-feedback">
                 {{ $message }}
            </div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning mt-2">
                
                Địa chỉ email của bạn chưa được xác thực.
                <button form="send-verification" class="btn btn-link p-0 text-decoration-none">
                    Click vào đây để gửi lại email xác thực.
                </button>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success mt-2">
                    
                    Link xác thực mới đã được gửi đến địa chỉ email của bạn.
                </div>
            @endif
        @endif
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary">
             Lưu thay đổi
        </button>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mb-0 py-2" role="alert">
                 Đã lưu thành công!
            </div>
        @endif
    </div>
</form>
