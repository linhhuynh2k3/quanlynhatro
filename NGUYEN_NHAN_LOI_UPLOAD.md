# Nguyên Nhân Lỗi Upload Hình Ảnh

## Khi Symbolic Link Đã Tồn Tại Nhưng Vẫn Lỗi

Nếu bạn đã có symbolic link `public/storage` nhưng vẫn không upload được hình, có thể do các nguyên nhân sau:

### 1. Symbolic Link Bị Hỏng (Broken Link) ⚠️

**Triệu chứng:**
- Symbolic link tồn tại nhưng trỏ đến đường dẫn không đúng
- Hình ảnh không hiển thị được trên web

**Kiểm tra:**
```powershell
# Kiểm tra symbolic link có đúng không
Get-Item public\storage | Select-Object LinkType, Target
```

**Fix:**
```powershell
# Xóa và tạo lại
Remove-Item public\storage -Force
php artisan storage:link
```

### 2. Symbolic Link Là Thư Mục Thật Thay Vì Link 🔴

**Triệu chứng:**
- `public/storage` là thư mục thật (có thể chứa file)
- Upload thành công nhưng hình không hiển thị
- Hình lưu vào `public/storage` thay vì `storage/app/public`

**Kiểm tra:**
```powershell
$item = Get-Item public\storage -Force
if ($item.LinkType -ne "SymbolicLink") {
    Write-Host "Đây là thư mục thật, không phải symbolic link!"
}
```

**Fix:**
```powershell
# Xóa thư mục và tạo symbolic link
Remove-Item public\storage -Recurse -Force
php artisan storage:link
```

### 3. Không Có Quyền Ghi Vào Storage 📝

**Triệu chứng:**
- Upload bị lỗi ngay lập tức
- Lỗi "Permission denied" trong logs

**Kiểm tra:**
```powershell
# Test quyền ghi
"test" | Out-File storage\app\public\test.txt
# Nếu lỗi thì không có quyền
```

**Fix:**
```powershell
# Cấp quyền ghi (Windows)
icacls storage /grant Users:F /T
icacls storage\app\public /grant Users:F /T
```

### 4. Cấu Hình PHP Upload Quá Nhỏ 🔧

**Triệu chứng:**
- Upload file nhỏ được, file lớn (>2MB) bị lỗi
- Lỗi "The file exceeds your upload_max_filesize"

**Kiểm tra:**
```powershell
php -i | findstr "upload_max_filesize"
php -i | findstr "post_max_size"
```

**Fix:**
- Mở file `php.ini` trong XAMPP
- Tìm và sửa:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  ```
- Restart Apache

### 5. Đường Dẫn Khác Nhau Giữa Các Máy 🖥️

**Triệu chứng:**
- Máy bạn chạy được, máy người khác không
- Symbolic link trỏ đến đường dẫn tuyệt đối cũ

**Kiểm tra:**
```powershell
# Xem symbolic link trỏ đến đâu
Get-Item public\storage | Select-Object Target
```

**Fix:**
- Symbolic link nên dùng đường dẫn tương đối (Laravel tự xử lý)
- Xóa và tạo lại trên máy mới:
  ```powershell
  Remove-Item public\storage -Force
  php artisan storage:link
  ```

### 6. Thư Mục Con Chưa Tồn Tại 📁

**Triệu chứng:**
- Upload bị lỗi "Directory not found"

**Kiểm tra:**
```powershell
Test-Path storage\app\public\listings
Test-Path storage\app\public\news
Test-Path storage\app\public\sliders
```

**Fix:**
```powershell
New-Item -ItemType Directory -Path storage\app\public\listings -Force
New-Item -ItemType Directory -Path storage\app\public\news -Force
New-Item -ItemType Directory -Path storage\app\public\sliders -Force
```

## Cách Kiểm Tra Tổng Hợp

Chạy script kiểm tra tự động:

```powershell
.\check_storage.ps1
```

Script này sẽ kiểm tra tất cả các vấn đề trên và báo cáo chi tiết.

## Cách Fix Tổng Hợp

Nếu không chắc nguyên nhân, chạy script fix đầy đủ:

```powershell
# Với quyền Administrator (khuyến nghị)
.\fix_storage.ps1
```

Hoặc fix thủ công từng bước:

```powershell
# 1. Xóa symbolic link cũ
Remove-Item public\storage -Force -Recurse

# 2. Đảm bảo thư mục tồn tại
New-Item -ItemType Directory -Path storage\app\public\listings -Force
New-Item -ItemType Directory -Path storage\app\public\news -Force
New-Item -ItemType Directory -Path storage\app\public\sliders -Force

# 3. Tạo symbolic link mới
php artisan storage:link

# 4. Kiểm tra quyền
icacls storage /grant Users:F /T

# 5. Xóa cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Kiểm Tra Logs

Nếu vẫn lỗi, xem logs để biết lỗi cụ thể:

```powershell
# Xem log mới nhất
Get-Content storage\logs\laravel.log -Tail 50
```

Tìm các dòng có chứa:
- "Permission denied"
- "Directory not found"
- "upload_max_filesize"
- "Failed to open stream"


