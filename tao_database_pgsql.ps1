# Script tạo database PostgreSQL cho dự án Homestay
# Chạy script này với quyền Administrator hoặc user có quyền tạo database

Write-Host "=== Tạo Database PostgreSQL cho Homestay ===" -ForegroundColor Green
Write-Host ""

# Thông tin từ file .env
$envFile = ".env"
if (-not (Test-Path $envFile)) {
    Write-Host "Lỗi: Không tìm thấy file .env" -ForegroundColor Red
    exit 1
}

# Đọc thông tin từ .env
$dbName = "homestay_db"
$dbUser = "postgres"
$dbPassword = "tranduc123"
$dbHost = "127.0.0.1"
$dbPort = "5432"

# Kiểm tra PostgreSQL có đang chạy không
Write-Host "Đang kiểm tra PostgreSQL..." -ForegroundColor Yellow
try {
    $pgTest = & psql --version 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "PostgreSQL không được cài đặt hoặc không có trong PATH"
    }
    Write-Host "✓ PostgreSQL đã được cài đặt" -ForegroundColor Green
} catch {
    Write-Host "✗ Lỗi: PostgreSQL không được tìm thấy" -ForegroundColor Red
    Write-Host "Vui lòng cài đặt PostgreSQL và đảm bảo psql có trong PATH" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "Thông tin kết nối:" -ForegroundColor Cyan
Write-Host "  Database: $dbName"
Write-Host "  User: $dbUser"
Write-Host "  Host: $dbHost"
Write-Host "  Port: $dbPort"
Write-Host ""

# Tạo database
Write-Host "Đang tạo database '$dbName'..." -ForegroundColor Yellow

# Sử dụng PGPASSWORD để tránh prompt password
$env:PGPASSWORD = $dbPassword

# Kiểm tra xem database đã tồn tại chưa
$checkDb = & psql -h $dbHost -p $dbPort -U $dbUser -d postgres -tAc "SELECT 1 FROM pg_database WHERE datname='$dbName'" 2>&1

if ($checkDb -eq "1") {
    Write-Host "⚠ Database '$dbName' đã tồn tại" -ForegroundColor Yellow
    $response = Read-Host "Bạn có muốn xóa và tạo lại? (y/N)"
    if ($response -eq "y" -or $response -eq "Y") {
        Write-Host "Đang xóa database cũ..." -ForegroundColor Yellow
        & psql -h $dbHost -p $dbPort -U $dbUser -d postgres -c "DROP DATABASE IF EXISTS $dbName;" 2>&1 | Out-Null
        Write-Host "✓ Đã xóa database cũ" -ForegroundColor Green
    } else {
        Write-Host "Giữ nguyên database hiện tại" -ForegroundColor Green
        exit 0
    }
}

# Tạo database mới
$createDb = & psql -h $dbHost -p $dbPort -U $dbUser -d postgres -c "CREATE DATABASE $dbName;" 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Đã tạo database '$dbName' thành công!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Bước tiếp theo:" -ForegroundColor Cyan
    Write-Host "  1. Chạy migrations: php artisan migrate" -ForegroundColor White
    Write-Host "  2. (Tùy chọn) Chạy seeders: php artisan db:seed" -ForegroundColor White
} else {
    Write-Host "✗ Lỗi khi tạo database:" -ForegroundColor Red
    Write-Host $createDb -ForegroundColor Red
    Write-Host ""
    Write-Host "Gợi ý:" -ForegroundColor Yellow
    Write-Host "  - Kiểm tra PostgreSQL đang chạy" -ForegroundColor White
    Write-Host "  - Kiểm tra username và password trong file .env" -ForegroundColor White
    Write-Host "  - Đảm bảo user '$dbUser' có quyền tạo database" -ForegroundColor White
}

# Xóa password khỏi environment
Remove-Item Env:\PGPASSWORD

