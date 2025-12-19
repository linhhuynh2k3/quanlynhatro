# Script đơn giản để fix lỗi "link already exists"
# Chạy script này khi gặp lỗi: "The [path] link already exists"

Write-Host "=== Fix Lỗi 'Link Already Exists' ===" -ForegroundColor Cyan
Write-Host ""

# Kiểm tra đang ở đúng thư mục
if (-not (Test-Path "artisan")) {
    Write-Host "Lỗi: Không tìm thấy file artisan. Vui lòng chạy script trong thư mục homestay!" -ForegroundColor Red
    exit 1
}

$publicStorageLink = "public\storage"

Write-Host "Đang xóa symbolic link cũ tại: $publicStorageLink" -ForegroundColor Yellow

if (Test-Path $publicStorageLink) {
    try {
        # Thử xóa với -Force
        Remove-Item $publicStorageLink -Force -ErrorAction Stop
        Write-Host "✓ Đã xóa thành công!" -ForegroundColor Green
    } catch {
        Write-Host "✗ Lỗi: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host ""
        Write-Host "Giải pháp:" -ForegroundColor Yellow
        Write-Host "1. Chạy PowerShell với quyền Administrator (Run as Administrator)" -ForegroundColor White
        Write-Host "2. Sau đó chạy lại lệnh:" -ForegroundColor White
        Write-Host "   Remove-Item public\storage -Force" -ForegroundColor Cyan
        Write-Host "   php artisan storage:link" -ForegroundColor Cyan
        exit 1
    }
} else {
    Write-Host "✓ Không có symbolic link cũ" -ForegroundColor Green
}

Write-Host ""
Write-Host "Đang tạo symbolic link mới..." -ForegroundColor Yellow
php artisan storage:link

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✓ Hoàn tất! Symbolic link đã được tạo thành công." -ForegroundColor Green
    Write-Host ""
    Write-Host "Kiểm tra:" -ForegroundColor Yellow
    Write-Host "- Thư mục public/storage phải là symbolic link (có mũi tên)" -ForegroundColor White
    Write-Host "- Thử upload hình ảnh lại" -ForegroundColor White
} else {
    Write-Host ""
    Write-Host "✗ Vẫn còn lỗi. Thử các bước sau:" -ForegroundColor Red
    Write-Host ""
    Write-Host "1. Chạy PowerShell với quyền Administrator" -ForegroundColor Yellow
    Write-Host "2. Chạy các lệnh:" -ForegroundColor Yellow
    Write-Host "   cd C:\xampp\htdocs\linh\homestay" -ForegroundColor Cyan
    Write-Host "   Remove-Item public\storage -Force -Recurse" -ForegroundColor Cyan
    Write-Host "   php artisan storage:link" -ForegroundColor Cyan
}


