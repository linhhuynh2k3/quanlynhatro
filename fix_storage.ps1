# Script tự động fix vấn đề upload và hiển thị hình ảnh
# Chạy script này trên máy mới hoặc khi gặp lỗi upload hình

Write-Host "=== Fix Storage và Upload Hình Ảnh ===" -ForegroundColor Cyan
Write-Host ""

# Kiểm tra đang ở đúng thư mục
if (-not (Test-Path "artisan")) {
    Write-Host "Lỗi: Không tìm thấy file artisan. Vui lòng chạy script trong thư mục homestay!" -ForegroundColor Red
    exit 1
}

Write-Host "[1/5] Kiểm tra thư mục storage/app/public..." -ForegroundColor Yellow
$storagePublicPath = "storage\app\public"
if (-not (Test-Path $storagePublicPath)) {
    Write-Host "Tạo thư mục storage/app/public..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $storagePublicPath -Force | Out-Null
    Write-Host "✓ Đã tạo thư mục" -ForegroundColor Green
} else {
    Write-Host "✓ Thư mục đã tồn tại" -ForegroundColor Green
}

# Tạo các thư mục con nếu chưa có
$subDirs = @("listings", "news", "sliders")
foreach ($dir in $subDirs) {
    $fullPath = Join-Path $storagePublicPath $dir
    if (-not (Test-Path $fullPath)) {
        New-Item -ItemType Directory -Path $fullPath -Force | Out-Null
        Write-Host "✓ Đã tạo thư mục $dir" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "[2/5] Xóa symbolic link cũ (nếu có)..." -ForegroundColor Yellow
$publicStorageLink = "public\storage"
if (Test-Path $publicStorageLink) {
    try {
        $item = Get-Item $publicStorageLink -Force -ErrorAction Stop
        
        # Kiểm tra xem có phải là symbolic link không
        $isLink = $false
        if ($item.LinkType -eq "SymbolicLink") {
            $isLink = $true
            Write-Host "  Phát hiện symbolic link cũ" -ForegroundColor Yellow
        } elseif ($item.Attributes -match "ReparsePoint") {
            $isLink = $true
            Write-Host "  Phát hiện reparse point (có thể là symbolic link)" -ForegroundColor Yellow
        } else {
            Write-Host "⚠ Cảnh báo: public/storage là thư mục thật, không phải symbolic link!" -ForegroundColor Yellow
        }
        
        # Xóa với -Force để bỏ qua lỗi
        if ($isLink) {
            Remove-Item $publicStorageLink -Force -ErrorAction Stop
            Write-Host "✓ Đã xóa symbolic link cũ" -ForegroundColor Green
        } else {
            # Nếu là thư mục thật, hỏi xem có muốn xóa không (cẩn thận!)
            Write-Host "  Đang xóa thư mục cũ để tạo symbolic link..." -ForegroundColor Yellow
            Remove-Item $publicStorageLink -Recurse -Force -ErrorAction Stop
            Write-Host "✓ Đã xóa thư mục cũ" -ForegroundColor Green
        }
    } catch {
        Write-Host "⚠ Không thể xóa: $($_.Exception.Message)" -ForegroundColor Yellow
        Write-Host "  Thử xóa thủ công bằng lệnh: Remove-Item public\storage -Force" -ForegroundColor Cyan
        Write-Host "  Hoặc chạy PowerShell với quyền Administrator" -ForegroundColor Cyan
    }
} else {
    Write-Host "✓ Không có symbolic link cũ" -ForegroundColor Green
}

Write-Host ""
Write-Host "[3/5] Tạo symbolic link mới..." -ForegroundColor Yellow
try {
    # Lấy đường dẫn tuyệt đối
    $targetPath = (Resolve-Path $storagePublicPath).Path
    $linkPath = Join-Path (Get-Location).Path "public\storage"
    
    # Tạo symbolic link (yêu cầu quyền admin trên Windows)
    $isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
    
    if ($isAdmin) {
        New-Item -ItemType SymbolicLink -Path $linkPath -Target $targetPath -Force | Out-Null
        Write-Host "✓ Đã tạo symbolic link thành công" -ForegroundColor Green
    } else {
        Write-Host "⚠ Không có quyền admin, thử dùng lệnh artisan..." -ForegroundColor Yellow
        Write-Host "  Chạy: php artisan storage:link" -ForegroundColor Cyan
        php artisan storage:link
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✓ Đã tạo symbolic link bằng artisan" -ForegroundColor Green
        } else {
            Write-Host "✗ Lỗi khi tạo symbolic link" -ForegroundColor Red
        }
    }
} catch {
    Write-Host "⚠ Lỗi khi tạo symbolic link: $($_.Exception.Message)" -ForegroundColor Yellow
    Write-Host "  Thử chạy: php artisan storage:link" -ForegroundColor Cyan
    php artisan storage:link
}

Write-Host ""
Write-Host "[4/5] Kiểm tra quyền truy cập thư mục..." -ForegroundColor Yellow
try {
    $testFile = Join-Path $storagePublicPath "test_write.tmp"
    "test" | Out-File -FilePath $testFile -Force
    if (Test-Path $testFile) {
        Remove-Item $testFile -Force
        Write-Host "✓ Có quyền ghi vào storage/app/public" -ForegroundColor Green
    }
} catch {
    Write-Host "✗ Không có quyền ghi vào storage/app/public!" -ForegroundColor Red
    Write-Host "  Vui lòng cấp quyền ghi cho thư mục storage" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "[5/5] Xóa cache..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan view:clear
Write-Host "✓ Đã xóa cache" -ForegroundColor Green

Write-Host ""
Write-Host "=== Hoàn tất! ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Kiểm tra:" -ForegroundColor Yellow
Write-Host "1. Thư mục public/storage phải là symbolic link trỏ đến storage/app/public" -ForegroundColor White
Write-Host "2. Thử upload hình ảnh trong admin panel" -ForegroundColor White
Write-Host "3. Nếu vẫn lỗi, kiểm tra logs: storage/logs/laravel.log" -ForegroundColor White
Write-Host ""
Write-Host "Nếu vẫn không được, chạy PowerShell với quyền Administrator và chạy lại script này." -ForegroundColor Yellow

