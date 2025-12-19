@extends('layouts.frontend')

@section('title', 'Hồ sơ cá nhân - Homestay.com')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <!-- Profile Sidebar -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 40px;">
                            
                        </div>
                    </div>
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                    <span class="badge 
                        @if(auth()->user()->isAdmin()) bg-danger
                        @elseif(auth()->user()->isLandlord()) bg-success
                        @else bg-info
                        @endif">
                        @if(auth()->user()->isAdmin()) Admin
                        @elseif(auth()->user()->isLandlord()) Chủ trọ
                        @else Người tìm phòng
                        @endif
                    </span>
                    @if(auth()->user()->isLandlord())
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted d-block">Số dư tài khoản</small>
                        <h4 class="text-success mb-0">{{ number_format(auth()->user()->balance) }} VNĐ</h4>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="card mt-3">
                <div class="list-group list-group-flush">
                    <a href="{{ route('home') }}" class="list-group-item list-group-item-action">
                         Về trang chủ
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                         Admin Dashboard
                    </a>
                    @elseif(auth()->user()->isLandlord())
                    <a href="{{ route('landlord.dashboard') }}" class="list-group-item list-group-item-action">
                         Dashboard
                    </a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- Update Profile Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"> Cập nhật thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"> Đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"> Xóa tài khoản</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
