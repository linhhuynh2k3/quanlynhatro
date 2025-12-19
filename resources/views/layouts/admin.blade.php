<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Homestay.com</title>
    
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
            background: #f5f7fa;
        }
        .admin-sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            min-height: 100vh;
            max-height: 100vh;
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            border-right: 1px solid #e0e0e0;
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .admin-sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 999px;
        }
        .admin-sidebar::-webkit-scrollbar-track {
            background-color: transparent;
        }
        .admin-sidebar nav {
            padding-bottom: 150px;
        }
        .admin-sidebar .account-block {
            position: sticky;
            bottom: 0;
            background: rgba(248, 249, 250, 0.95);
            backdrop-filter: blur(4px);
        }
        .admin-sidebar .logo {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .admin-sidebar .logo h4,
        .admin-sidebar .logo small {
            color: white !important;
        }
        .admin-sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .admin-sidebar .nav-link:hover {
            background: #e9ecef;
            color: #667eea;
        }
        .admin-sidebar .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .admin-sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .admin-content {
            margin-left: 260px;
            min-height: 100vh;
        }
        .admin-header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .admin-main {
            padding: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
            .admin-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="logo">
            <h4 class="text-white mb-0">Admin Panel</h4>
            <small class="text-white-50">Homestay.com</small>
        </div>
        
        <nav class="mt-3">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Người dùng
            </a>
            <a href="{{ route('admin.listings.index') }}" class="nav-link {{ request()->routeIs('admin.listings.*') ? 'active' : '' }}">
                <i class="bi bi-file-post"></i> Bài đăng
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder"></i> Danh mục
            </a>
            <a href="{{ route('admin.sliders.index') }}" class="nav-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                <i class="bi bi-images"></i> Slider
            </a>
            <a href="{{ route('admin.news.index') }}" class="nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i> Tin tức
            </a>
            <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> Thanh toán
            </a>
            <a href="{{ route('admin.withdrawals.index') }}" class="nav-link {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                <i class="bi bi-bank"></i> Rút tiền
            </a>
            <a href="{{ route('admin.feedback.index') }}" class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-text"></i> Phản hồi
            </a>
            <a href="{{ route('admin.landlord-requests.index') }}" class="nav-link {{ request()->routeIs('admin.landlord-requests.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Yêu cầu chủ trọ
            </a>
        </nav>
        
        <div class="account-block mt-auto border-top border-secondary bg-light p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <small class="text-dark d-block fw-bold">{{ auth()->user()->name }}</small>
                    <small class="text-muted">Admin</small>
                </div>
            </div>
            <a href="{{ route('home') }}" class="nav-link text-dark">
                <i class="bi bi-house"></i> Về trang chủ
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="nav-link text-dark w-100 border-0 bg-transparent p-0 text-start">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Header -->
        <header class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-outline-secondary d-md-none" onclick="toggleSidebar()">Menu</button>
                    <h2 class="mb-0 ms-2 d-inline">@yield('page-title', 'Dashboard')</h2>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted">{{ \App\Helpers\DateTimeHelper::formatDateTime(now()) }}</span>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="admin-main">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('show');
        }
        
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
