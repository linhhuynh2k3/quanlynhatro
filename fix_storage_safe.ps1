# Script fix storage an toàn - xử lý lỗi EBUSY
# Chạy script này khi gặp lỗi "resource busy or locked"

Write-Host "=== Fix Storage (An Toàn) ===" -ForegroundColor Cyan
Write-Host ""

# Kiểm tra đang ở đúng thư mục
if (-not (Test-Path "artisan")) {
    Write-Host "Lỗi: Không tìm thấy file artisan. Vui lòng chạy script trong thư mục homestay!" -ForegroundColor Red
    exit 1
}

$publicStoragePath = "public\storage"

Write-Host "[1/5] Kiểm tra public/storage..." -ForegroundColor Yellow
if (Test-Path $publicStoragePath) {
    $item = Get-Item $publicStoragePath -Force
    Write-Host "  Phát hiện: $($item.LinkType)" -ForegroundColor Gray
    
    if ($item.LinkType -eq "SymbolicLink") {
        Write-Host "  ✓ Đã là symbolic link đúng, không cần fix!" -ForegroundColor Green
        exit 0
    }
} else {
    Write-Host "  ✓ Không có public/storage (sẽ tạo mới)" -ForegroundColor Green
    Write-Host ""
    Write-Host "[2/5] Tạo symbolic link mới..." -ForegroundColor Yellow
    php artisan storage:link
    if ($LASTEXITCODE -eq 0) {
        Write-Host "  ✓ Hoàn tất!" -ForegroundColor Green
    }
    exit 0
}

Write-Host ""
Write-Host "[2/5] Đóng các chương trình đang sử dụng thư mục..." -ForegroundColor Yellow
Write-Host "  Vui lòng:" -ForegroundColor Yellow
Write-Host "  1. Đóng VS Code (nếu đang mở)" -ForegroundColor White
Write-Host "  2. Đóng File Explorer đang mở thư mục public/storage" -ForegroundColor White
Write-Host "  3. Đóng web browser" -ForegroundColor White
Write-Host "  4. Dừng Apache/XAMPP (nếu đang chạy)" -ForegroundColor White
Write-Host ""
$response = Read-Host "  Đã đóng tất cả? (Y/N)"
if ($response -ne "Y" -and $response -ne "y") {
    Write-Host "  Vui lòng đóng các chương trình và chạy lại script!" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "[3/5] Đợi 2 giây để giải phóng lock..." -ForegroundColor Yellow
Start-Sleep -Seconds 2

Write-Host ""
Write-Host "[4/5] Thử xóa public/storage..." -ForegroundColor Yellow
$maxRetries = 3
$retryCount = 0
$success = $false

while ($retryCount -lt $maxRetries -and -not $success) {
    $retryCount++
    Write-Host "  Lần thử $retryCount/$maxRetries..." -ForegroundColor Gray
    
    try {
        # Thử xóa bằng cách khác nhau
        if (Test-Path $publicStoragePath) {
            $item = Get-Item $publicStoragePath -Force
            
            if ($item.LinkType -eq "SymbolicLink" -or $item.LinkType -eq "Junction") {
                # Xóa symbolic link/junction
                cmd /c rmdir "$publicStoragePath" 2>$null
            } else {
                # Xóa thư mục thật
                Remove-Item $publicStoragePath -Recurse -Force -ErrorAction Stop
            }
            
            # Kiểm tra đã xóa chưa
            Start-Sleep -Milliseconds 500
            if (-not (Test-Path $publicStoragePath)) {
                $success = $true
                Write-Host "  ✓ Đã xóa thành công!" -ForegroundColor Green
            }
        } else {
            $success = $true
            Write-Host "  ✓ Đã xóa (hoặc không tồn tại)" -ForegroundColor Green
        }
    } catch {
        Write-Host "  ✗ Lỗi: $($_.Exception.Message)" -ForegroundColor Red
        if ($retryCount -lt $maxRetries) {
            Write-Host "  Đợi 2 giây và thử lại..." -ForegroundColor Yellow
            Start-Sleep -Seconds 2
        }
    }
}

if (-not $success) {
    Write-Host ""
    Write-Host "✗ Không thể xóa thư mục sau $maxRetries lần thử!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Giải pháp thay thế:" -ForegroundColor Yellow
    Write-Host "1. Restart máy tính" -ForegroundColor White
    Write-Host "2. Hoặc chạy PowerShell với quyền Administrator:" -ForegroundColor White
    Write-Host "   Remove-Item public\storage -Recurse -Force" -ForegroundColor Cyan
    Write-Host "   php artisan storage:link" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "3. Hoặc đổi tên thư mục thay vì xóa:" -ForegroundColor White
    Write-Host "   Rename-Item public\storage public\storage_old" -ForegroundColor Cyan
    Write-Host "   php artisan storage:link" -ForegroundColor Cyan
    exit 1
}

Write-Host ""
Write-Host "[5/5] Tạo symbolic link mới..." -ForegroundColor Yellow
php artisan storage:link

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✓ Hoàn tất! Symbolic link đã được tạo thành công." -ForegroundColor Green
    Write-Host ""
    Write-Host "Kiểm tra:" -ForegroundColor Yellow
    Write-Host "- public/storage phải là symbolic link (có mũi tên)" -ForegroundColor White
    Write-Host "- Thử upload hình và kiểm tra hiển thị" -ForegroundColor White
} else {
    Write-Host ""
    Write-Host "✗ Lỗi khi tạo symbolic link" -ForegroundColor Red
    Write-Host "Thử chạy: php artisan storage:link" -ForegroundColor Yellow
}


