<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cho Thuê Phòng Trọ, Nhà Trọ, Mặt Bằng - Homestay.com')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <!-- Header Top -->
    <div class="header-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-4">
                        <span>Hotline: 0909 316 890</span>
                        <span>Email: support@homestay.com</span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    @auth
                        <span>Xin chào, <strong>{{ auth()->user()->name }}</strong></span>
                    @else
                        <a href="{{ route('login') }}" class="text-white text-decoration-none me-3">Đăng nhập</a>
                        <span class="text-white">|</span>
                        <a href="{{ route('register') }}" class="text-white text-decoration-none ms-3">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Header Main -->
    <header class="header-main">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                    Homestay<span class="text-muted">.com</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto me-4">
                        @forelse(($headerCategories ?? []) as $category)
                            @if($category->children->isNotEmpty())
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="{{ route('listings.index', ['category_id' => $category->id]) }}" data-bs-toggle="dropdown">
                                        {{ $category->name }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('listings.index', ['category_id' => $category->id]) }}">
                                                Tất cả {{ strtolower($category->name) }}
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @foreach($category->children as $child)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('listings.index', ['category_id' => $child->id]) }}">
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('listings.index', ['category_id' => $category->id]) }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endif
                        @empty
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('listings.index') }}">Danh mục</a>
                            </li>
                        @endforelse
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('news.index') }}">Tin tức</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('listings.map') }}">
                                <i class="bi bi-map"></i> Bản đồ
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center gap-3">
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">Admin</a>
                            @elseif(auth()->user()->isLandlord())
                                <a href="{{ route('landlord.dashboard') }}" class="btn btn-outline-primary btn-sm">Quản lý</a>
                            @elseif(auth()->user()->isTenant())
                                <a href="{{ route('bookings.index') }}" class="btn btn-outline-primary btn-sm">Đặt thuê</a>
                            @endif
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    {{ auth()->user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Hồ sơ</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">Đăng xuất</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-danger btn-sm">Đăng ký</a>
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5>Homestay.com</h5>
                    <p class="text-white-50">Kênh thông tin cho thuê phòng trọ, nhà trọ, mặt bằng số 1 Việt Nam. Tìm kiếm và đăng tin miễn phí.</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Danh mục</h5>
                    <ul>
                        <li><a href="{{ route('listings.index') }}">Phòng trọ</a></li>
                        <li><a href="{{ route('listings.index', ['category_id' => 2]) }}">Nhà nguyên căn</a></li>
                        <li><a href="{{ route('listings.index', ['category_id' => 3]) }}">Căn hộ</a></li>
                        <li><a href="{{ route('listings.index', ['category_id' => 4]) }}">Mặt bằng</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Hỗ trợ</h5>
                    <ul>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                        <li><a href="#">Hướng dẫn đăng tin</a></li>
                        <li><a href="#">Quy định sử dụng</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Liên hệ</h5>
                    <ul>
                        <li>Điện thoại: 0909 316 890</li>
                        <li>Email: support@homestay.com</li>
                        <li>Địa chỉ: 123 Đường ABC, Quận 1, TP.HCM</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Homestay.com. All rights reserved. | Thiết kế bởi Homestay Team</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @yield('scripts')
</body>
</html>
