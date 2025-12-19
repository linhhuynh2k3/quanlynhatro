# Script kiểm tra chi tiết vấn đề storage và upload
# Chạy script này để tìm nguyên nhân lỗi upload hình ảnh

Write-Host "=== Kiểm Tra Storage và Upload ===" -ForegroundColor Cyan
Write-Host ""

# Kiểm tra đang ở đúng thư mục
if (-not (Test-Path "artisan")) {
    Write-Host "Lỗi: Không tìm thấy file artisan. Vui lòng chạy script trong thư mục homestay!" -ForegroundColor Red
    exit 1
}

$errors = @()
$warnings = @()

# 1. Kiểm tra thư mục storage/app/public
Write-Host "[1] Kiểm tra thư mục storage/app/public..." -ForegroundColor Yellow
$storagePublicPath = "storage\app\public"
if (Test-Path $storagePublicPath) {
    Write-Host "  ✓ Thư mục tồn tại" -ForegroundColor Green
    
    # Kiểm tra quyền ghi
    try {
        $testFile = Join-Path $storagePublicPath "test_write_$(Get-Date -Format 'yyyyMMddHHmmss').tmp"
        "test" | Out-File -FilePath $testFile -Force -ErrorAction Stop
        if (Test-Path $testFile) {
            Remove-Item $testFile -Force
            Write-Host "  ✓ Có quyền ghi" -ForegroundColor Green
        }
    } catch {
        $errors += "Không có quyền ghi vào storage/app/public: $($_.Exception.Message)"
        Write-Host "  ✗ Không có quyền ghi!" -ForegroundColor Red
    }
} else {
    $errors += "Thư mục storage/app/public không tồn tại"
    Write-Host "  ✗ Thư mục không tồn tại" -ForegroundColor Red
}

# 2. Kiểm tra symbolic link
Write-Host ""
Write-Host "[2] Kiểm tra symbolic link public/storage..." -ForegroundColor Yellow
$publicStorageLink = "public\storage"
if (Test-Path $publicStorageLink) {
    try {
        $item = Get-Item $publicStorageLink -Force -ErrorAction Stop
        
        # Kiểm tra xem có phải là symbolic link không
        if ($item.LinkType -eq "SymbolicLink") {
            Write-Host "  ✓ Là symbolic link" -ForegroundColor Green
            
            # Kiểm tra target có tồn tại không
            $target = $item.Target
            if ($target) {
                Write-Host "  Target: $target" -ForegroundColor Gray
                if (Test-Path $target) {
                    Write-Host "  ✓ Target tồn tại" -ForegroundColor Green
                } else {
                    $errors += "Symbolic link trỏ đến đường dẫn không tồn tại: $target"
                    Write-Host "  ✗ Target không tồn tại!" -ForegroundColor Red
                }
            } else {
                # Thử đọc target bằng cách khác
                $resolved = (Get-Item $publicStorageLink).Target
                if ($resolved) {
                    Write-Host "  Target: $resolved" -ForegroundColor Gray
                    if (Test-Path $resolved) {
                        Write-Host "  ✓ Target tồn tại" -ForegroundColor Green
                    } else {
                        $errors += "Symbolic link trỏ đến đường dẫn không tồn tại: $resolved"
                        Write-Host "  ✗ Target không tồn tại!" -ForegroundColor Red
                    }
                }
            }
            
            # Kiểm tra xem có thể truy cập file qua link không
            $testImagePath = Join-Path $publicStorageLink "test.txt"
            try {
                $testContent = Get-Content (Join-Path $storagePublicPath "test.txt") -ErrorAction SilentlyContinue
                Write-Host "  ✓ Có thể truy cập qua link" -ForegroundColor Green
            } catch {
                $warnings += "Không thể truy cập file qua symbolic link"
                Write-Host "  ⚠ Không thể truy cập qua link" -ForegroundColor Yellow
            }
        } elseif ($item.Attributes -match "ReparsePoint") {
            Write-Host "  ⚠ Là reparse point (có thể là junction)" -ForegroundColor Yellow
            $warnings += "public/storage là reparse point, nên là symbolic link"
        } else {
            $errors += "public/storage là thư mục thật, không phải symbolic link!"
            Write-Host "  ✗ Là thư mục thật, không phải symbolic link!" -ForegroundColor Red
            Write-Host "    Điều này sẽ gây lỗi khi hiển thị hình ảnh" -ForegroundColor Red
        }
    } catch {
        $errors += "Không thể kiểm tra symbolic link: $($_.Exception.Message)"
        Write-Host "  ✗ Lỗi khi kiểm tra: $($_.Exception.Message)" -ForegroundColor Red
    }
} else {
    $errors += "Symbolic link public/storage không tồn tại"
    Write-Host "  ✗ Symbolic link không tồn tại" -ForegroundColor Red
}

# 3. Kiểm tra cấu hình filesystem
Write-Host ""
Write-Host "[3] Kiểm tra cấu hình filesystem..." -ForegroundColor Yellow
$configPath = "config\filesystems.php"
if (Test-Path $configPath) {
    $configContent = Get-Content $configPath -Raw
    if ($configContent -match "storage_path\('app/public'\)") {
        Write-Host "  ✓ Cấu hình đúng" -ForegroundColor Green
    } else {
        $warnings += "Cấu hình filesystem có thể không đúng"
        Write-Host "  ⚠ Cần kiểm tra cấu hình" -ForegroundColor Yellow
    }
} else {
    $errors += "Không tìm thấy file config/filesystems.php"
    Write-Host "  ✗ Không tìm thấy config" -ForegroundColor Red
}

# 4. Kiểm tra PHP upload settings
Write-Host ""
Write-Host "[4] Kiểm tra cấu hình PHP upload..." -ForegroundColor Yellow
try {
    $phpInfo = php -i 2>&1 | Out-String
    
    if ($phpInfo -match "upload_max_filesize\s+=>\s+(\d+[KMGT]?)") {
        $uploadMax = $matches[1]
        Write-Host "  upload_max_filesize: $uploadMax" -ForegroundColor Gray
        
        # Chuyển đổi sang KB để so sánh
        $uploadMaxKB = 0
        if ($uploadMax -match "(\d+)M") {
            $uploadMaxKB = [int]$matches[1] * 1024
        } elseif ($uploadMax -match "(\d+)K") {
            $uploadMaxKB = [int]$matches[1]
        }
        
        if ($uploadMaxKB -lt 2048) {
            $warnings += "upload_max_filesize ($uploadMax) nhỏ hơn 2MB, có thể gây lỗi upload file lớn"
            Write-Host "  ⚠ upload_max_filesize quá nhỏ (< 2MB)" -ForegroundColor Yellow
        } else {
            Write-Host "  ✓ upload_max_filesize đủ lớn" -ForegroundColor Green
        }
    }
    
    if ($phpInfo -match "post_max_size\s+=>\s+(\d+[KMGT]?)") {
        $postMax = $matches[1]
        Write-Host "  post_max_size: $postMax" -ForegroundColor Gray
    }
} catch {
    $warnings += "Không thể kiểm tra cấu hình PHP"
    Write-Host "  ⚠ Không thể kiểm tra PHP config" -ForegroundColor Yellow
}

# 5. Kiểm tra thư mục con
Write-Host ""
Write-Host "[5] Kiểm tra thư mục con..." -ForegroundColor Yellow
$subDirs = @("listings", "news", "sliders")
foreach ($dir in $subDirs) {
    $fullPath = Join-Path $storagePublicPath $dir
    if (Test-Path $fullPath) {
        Write-Host "  ✓ $dir tồn tại" -ForegroundColor Green
    } else {
        $warnings += "Thư mục $dir chưa tồn tại"
        Write-Host "  ⚠ $dir chưa tồn tại" -ForegroundColor Yellow
    }
}

# Tóm tắt
Write-Host ""
Write-Host "=== TÓM TẮT ===" -ForegroundColor Cyan
Write-Host ""

if ($errors.Count -eq 0 -and $warnings.Count -eq 0) {
    Write-Host "✓ Không phát hiện vấn đề nào!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Nếu vẫn lỗi upload, kiểm tra:" -ForegroundColor Yellow
    Write-Host "1. Logs: storage/logs/laravel.log" -ForegroundColor White
    Write-Host "2. Kiểm tra form upload có đúng enctype='multipart/form-data' không" -ForegroundColor White
    Write-Host "3. Kiểm tra network tab trong browser console khi upload" -ForegroundColor White
} else {
    if ($errors.Count -gt 0) {
        Write-Host "✗ LỖI NGHIÊM TRỌNG ($($errors.Count)):" -ForegroundColor Red
        foreach ($error in $errors) {
            Write-Host "  - $error" -ForegroundColor Red
        }
        Write-Host ""
    }
    
    if ($warnings.Count -gt 0) {
        Write-Host "⚠ CẢNH BÁO ($($warnings.Count)):" -ForegroundColor Yellow
        foreach ($warning in $warnings) {
            Write-Host "  - $warning" -ForegroundColor Yellow
        }
        Write-Host ""
    }
    
    Write-Host "Giải pháp:" -ForegroundColor Cyan
    Write-Host "1. Chạy script fix: .\fix_storage.ps1" -ForegroundColor White
    Write-Host "2. Hoặc fix thủ công theo từng lỗi ở trên" -ForegroundColor White
}

Write-Host ""


