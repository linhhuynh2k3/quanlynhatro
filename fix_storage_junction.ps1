# Script fix vấn đề: public/storage là thư mục thật/junction thay vì symbolic link
# Vấn đề: File chỉ lưu vào storage/app/public, nhưng cần symbolic link để hiển thị

Write-Host "=== Fix Storage: Xóa Junction và Tạo Symbolic Link ===" -ForegroundColor Cyan
Write-Host ""

# Kiểm tra đang ở đúng thư mục
if (-not (Test-Path "artisan")) {
    Write-Host "Lỗi: Không tìm thấy file artisan. Vui lòng chạy script trong thư mục homestay!" -ForegroundColor Red
    exit 1
}

$publicStoragePath = "public\storage"
$storagePublicPath = "storage\app\public"

Write-Host "[1/4] Kiểm tra public/storage..." -ForegroundColor Yellow
if (Test-Path $publicStoragePath) {
    $item = Get-Item $publicStoragePath -Force
    Write-Host "  Phát hiện: $($item.LinkType) - $($item.Attributes)" -ForegroundColor Gray
    
    if ($item.LinkType -eq "SymbolicLink") {
        Write-Host "  ✓ Đã là symbolic link đúng" -ForegroundColor Green
        Write-Host "  Không cần fix!" -ForegroundColor Green
        exit 0
    } else {
        Write-Host "  ⚠ Đây là Junction/Thư mục thật, không phải symbolic link!" -ForegroundColor Yellow
        Write-Host "  Cần xóa và tạo lại symbolic link..." -ForegroundColor Yellow
    }
} else {
    Write-Host "  ✓ Không có public/storage (sẽ tạo mới)" -ForegroundColor Green
}

Write-Host ""
Write-Host "[2/4] Đảm bảo storage/app/public tồn tại..." -ForegroundColor Yellow
if (-not (Test-Path $storagePublicPath)) {
    Write-Host "  Tạo thư mục storage/app/public..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $storagePublicPath -Force | Out-Null
    Write-Host "  ✓ Đã tạo" -ForegroundColor Green
} else {
    Write-Host "  ✓ Thư mục đã tồn tại" -ForegroundColor Green
}

# Tạo các thư mục con
$subDirs = @("listings", "news", "sliders")
foreach ($dir in $subDirs) {
    $fullPath = Join-Path $storagePublicPath $dir
    if (-not (Test-Path $fullPath)) {
        New-Item -ItemType Directory -Path $fullPath -Force | Out-Null
        Write-Host "  ✓ Đã tạo thư mục $dir" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "[3/4] Xóa public/storage cũ (junction/thư mục thật)..." -ForegroundColor Yellow
if (Test-Path $publicStoragePath) {
    try {
        # Thử xóa như junction
        cmd /c rmdir "$publicStoragePath" 2>$null
        if (Test-Path $publicStoragePath) {
            # Nếu vẫn còn, thử xóa như thư mục
            Remove-Item $publicStoragePath -Recurse -Force -ErrorAction Stop
        }
        Write-Host "  ✓ Đã xóa thành công" -ForegroundColor Green
    } catch {
        Write-Host "  ✗ Lỗi khi xóa: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host ""
        Write-Host "  Vui lòng:" -ForegroundColor Yellow
        Write-Host "  1. Đóng tất cả chương trình đang mở thư mục public/storage" -ForegroundColor White
        Write-Host "  2. Chạy PowerShell với quyền Administrator" -ForegroundColor White
        Write-Host "  3. Chạy lại script này" -ForegroundColor White
        exit 1
    }
} else {
    Write-Host "  ✓ Không có để xóa" -ForegroundColor Green
}

Write-Host ""
Write-Host "[4/4] Tạo symbolic link mới..." -ForegroundColor Yellow
try {
    # Lấy đường dẫn tuyệt đối
    $targetPath = (Resolve-Path $storagePublicPath).Path
    $linkPath = Join-Path (Get-Location).Path "public\storage"
    
    # Kiểm tra quyền admin
    $isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
    
    if ($isAdmin) {
        # Tạo symbolic link với quyền admin
        New-Item -ItemType SymbolicLink -Path $linkPath -Target $targetPath -Force | Out-Null
        Write-Host "  ✓ Đã tạo symbolic link thành công" -ForegroundColor Green
    } else {
        # Thử dùng lệnh artisan (có thể không cần admin)
        Write-Host "  Thử dùng lệnh artisan..." -ForegroundColor Yellow
        $result = php artisan storage:link 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  ✓ Đã tạo symbolic link bằng artisan" -ForegroundColor Green
        } else {
            Write-Host "  ✗ Lỗi: $result" -ForegroundColor Red
            Write-Host ""
            Write-Host "  Vui lòng chạy PowerShell với quyền Administrator và chạy lại:" -ForegroundColor Yellow
            Write-Host "  php artisan storage:link" -ForegroundColor Cyan
            exit 1
        }
    }
} catch {
    Write-Host "  ✗ Lỗi: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "  Thử chạy thủ công:" -ForegroundColor Yellow
    Write-Host "  php artisan storage:link" -ForegroundColor Cyan
    exit 1
}

# Kiểm tra kết quả
Write-Host ""
Write-Host "Kiểm tra kết quả..." -ForegroundColor Yellow
if (Test-Path $publicStoragePath) {
    $newItem = Get-Item $publicStoragePath -Force
    if ($newItem.LinkType -eq "SymbolicLink") {
        Write-Host "  ✓ Symbolic link đã được tạo đúng!" -ForegroundColor Green
        Write-Host "  Target: $($newItem.Target)" -ForegroundColor Gray
    } else {
        Write-Host "  ⚠ Vẫn chưa phải symbolic link: $($newItem.LinkType)" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "=== Hoàn tất! ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Lưu ý:" -ForegroundColor Yellow
Write-Host "1. File vẫn lưu vào storage/app/public (đúng)" -ForegroundColor White
Write-Host "2. Symbolic link public/storage sẽ trỏ đến storage/app/public" -ForegroundColor White
Write-Host "3. Trên máy người khác, cần chạy: php artisan storage:link" -ForegroundColor White
Write-Host ""
Write-Host "Kiểm tra:" -ForegroundColor Yellow
Write-Host "- public/storage phải là symbolic link (có mũi tên)" -ForegroundColor White
Write-Host "- Thử upload hình và kiểm tra hiển thị" -ForegroundColor White


