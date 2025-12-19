<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Chủ Trọ') - Homestay.com</title>
    
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
        .landlord-sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            height: 100vh;
            width: 260px;
            min-width: 200px;
            max-width: 500px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            border-right: 1px solid #e0e0e0;
            resize: horizontal;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .landlord-sidebar-resizer {
            position: absolute;
            right: 0;
            top: 0;
            width: 5px;
            height: 100%;
            cursor: col-resize;
            background: transparent;
            z-index: 1001;
        }
        .landlord-sidebar-resizer:hover {
            background: rgba(26, 188, 156, 0.3);
        }
        .landlord-sidebar .logo {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
            flex-shrink: 0;
        }
        .landlord-sidebar nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 10px;
        }
        .landlord-sidebar .sidebar-bottom {
            flex-shrink: 0;
            padding: 15px;
            border-top: 1px solid #e0e0e0;
            background: #f8f9fa;
        }
        .landlord-sidebar .logo h4,
        .landlord-sidebar .logo small {
            color: white !important;
        }
        .landlord-sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .landlord-sidebar .nav-link:hover {
            background: #e9ecef;
            color: #1abc9c;
        }
        .landlord-sidebar .nav-link.active {
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
            color: white;
        }
        .landlord-sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .landlord-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: margin-left 0.2s ease;
        }
        .landlord-header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .landlord-main {
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
            .landlord-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .landlord-sidebar.show {
                transform: translateX(0);
            }
            .landlord-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="landlord-sidebar" id="landlordSidebar">
        <div class="landlord-sidebar-resizer" id="sidebarResizer"></div>
        <div class="logo">
            <h4 class="text-white mb-0">Landlord Panel</h4>
            <small class="text-white-50">Homestay.com</small>
        </div>
        
        <nav class="mt-3">
            <a href="{{ route('landlord.dashboard') }}" class="nav-link {{ request()->routeIs('landlord.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('landlord.listings.index') }}" class="nav-link {{ request()->routeIs('landlord.listings.*') ? 'active' : '' }}">
                <i class="bi bi-buildings"></i> Bài đăng
            </a>
            <a href="{{ route('landlord.listings.create') }}" class="nav-link {{ request()->routeIs('landlord.listings.create') ? 'active' : '' }}">
                <i class="bi bi-plus-square"></i> Đăng bài mới
            </a>
            <a href="{{ route('landlord.payments.index') }}" class="nav-link {{ request()->routeIs('landlord.payments.index') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Nạp tiền
            </a>
            <a href="{{ route('landlord.payments.history') }}" class="nav-link {{ request()->routeIs('landlord.payments.history') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i> Lịch sử thanh toán
            </a>
            <a href="{{ route('landlord.withdrawals.index') }}" class="nav-link {{ request()->routeIs('landlord.withdrawals.*') ? 'active' : '' }}">
                <i class="bi bi-bank"></i> Rút tiền
            </a>
            <a href="{{ route('landlord.bookings.index') }}" class="nav-link {{ request()->routeIs('landlord.bookings.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Đơn đặt thuê
            </a>
            <a href="{{ route('landlord.rooms.index') }}" class="nav-link {{ request()->routeIs('landlord.rooms.*') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Quản lý phòng
            </a>
            <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i> Tin nhắn
                @php
                    // Tối ưu: đếm trực tiếp từ messages thay vì load tất cả conversations
                    $unreadCount = \App\Models\Message::whereHas('conversation', function($q) {
                        $q->where('landlord_id', auth()->id());
                    })
                    ->where('sender_id', '!=', auth()->id())
                    ->where('is_read', false)
                    ->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
                @endif
            </a>
        </nav>
        
        <div class="sidebar-bottom">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <small class="text-dark d-block fw-bold">{{ auth()->user()->name }}</small>
                    <small class="text-muted">Số dư: {{ number_format(auth()->user()->balance) }} VNĐ</small>
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
    <div class="landlord-content">
        <!-- Header -->
        <header class="landlord-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button class="btn btn-outline-secondary d-md-none" onclick="toggleSidebar()">Menu</button>
                        <h2 class="mb-0 ms-2 d-inline">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end">
                            <small class="text-muted d-block">Số dư tài khoản</small>
                            <strong class="text-success">{{ number_format(auth()->user()->balance) }} VNĐ</strong>
                        </div>
                    </div>
                </div>
        </header>

        <!-- Main -->
        <main class="landlord-main">
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
            document.querySelector('.landlord-sidebar').classList.toggle('show');
        }
        
        // Sidebar Resize Functionality
        (function() {
            const sidebar = document.getElementById('landlordSidebar');
            const resizer = document.getElementById('sidebarResizer');
            const content = document.querySelector('.landlord-content');
            
            if (!sidebar || !resizer) return;
            
            let isResizing = false;
            let startX, startWidth;
            
            resizer.addEventListener('mousedown', function(e) {
                isResizing = true;
                startX = e.clientX;
                startWidth = parseInt(window.getComputedStyle(sidebar).width, 10);
                document.body.style.cursor = 'col-resize';
                document.body.style.userSelect = 'none';
                e.preventDefault();
            });
            
            document.addEventListener('mousemove', function(e) {
                if (!isResizing) return;
                
                const width = startWidth + (e.clientX - startX);
                const minWidth = 200;
                const maxWidth = 500;
                
                if (width >= minWidth && width <= maxWidth) {
                    sidebar.style.width = width + 'px';
                    content.style.marginLeft = width + 'px';
                    // Lưu vào localStorage
                    localStorage.setItem('landlordSidebarWidth', width);
                }
            });
            
            document.addEventListener('mouseup', function() {
                if (isResizing) {
                    isResizing = false;
                    document.body.style.cursor = '';
                    document.body.style.userSelect = '';
                }
            });
            
            // Khôi phục width từ localStorage
            const savedWidth = localStorage.getItem('landlordSidebarWidth');
            if (savedWidth) {
                sidebar.style.width = savedWidth + 'px';
                content.style.marginLeft = savedWidth + 'px';
            }
        })();
        
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
