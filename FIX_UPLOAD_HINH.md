# Fix Lỗi Upload Hình Ảnh

## Vấn đề
Khi upload hình ảnh ở tin tức hoặc phòng trọ, trên máy bạn được nhưng trên máy người khác không được.

**Triệu chứng cụ thể:**
- File chỉ lưu vào `storage/app/public` (đúng)
- Nhưng trên máy người khác không hiển thị được
- Trên máy bạn có file ở cả `storage/app/public` VÀ `public/storage`

## Nguyên nhân

**Vấn đề chính:** `public/storage` là **junction/thư mục thật** thay vì **symbolic link**

- Trên máy bạn: `public/storage` là junction nên file "sao chép" vào cả 2 nơi
- Trên máy người khác: Không có junction/symbolic link nên không hiển thị được
- Laravel cần **symbolic link** từ `public/storage` → `storage/app/public` để hiển thị hình ảnh

**Cách hoạt động đúng:**
1. File lưu vào `storage/app/public` (duy nhất)
2. Symbolic link `public/storage` trỏ đến `storage/app/public`
3. Web truy cập `/storage/...` → qua symbolic link → đến file thật

## Cách Fix Nhanh

### Trên máy bạn (có junction cần fix):

1. **Chạy script fix junction:**
   ```powershell
   cd C:\xampp\htdocs\linh\homestay
   .\fix_storage_junction.ps1
   ```
   Script này sẽ:
   - Xóa junction/thư mục thật `public/storage`
   - Tạo symbolic link đúng cách

2. **Hoặc fix thủ công (với quyền Admin):**
   ```powershell
   # Xóa junction/thư mục thật
   Remove-Item public\storage -Recurse -Force
   
   # Tạo symbolic link mới
   php artisan storage:link
   ```

### Trên máy người khác (chưa có link):

1. **Mở PowerShell** và chạy:
   ```powershell
   cd C:\xampp\htdocs\linh\homestay
   php artisan storage:link
   ```

2. **Kiểm tra:**
   - Mở thư mục `public/storage` 
   - Phải thấy có mũi tên nhỏ (symbolic link)
   - Thử upload hình lại

## Kiểm tra đã fix chưa

1. ✅ Thư mục `public/storage` là symbolic link (có mũi tên)
2. ✅ Upload hình ảnh thành công
3. ✅ Hình ảnh hiển thị được trên web (URL: `/storage/listings/...`)

## Lỗi "The [path] link already exists"

Nếu gặp lỗi này khi chạy `php artisan storage:link`:

**Cách fix nhanh:**
```powershell
# Chạy script đơn giản
.\fix_storage_simple.ps1
```

**Hoặc fix thủ công:**
```powershell
# 1. Xóa symbolic link cũ
Remove-Item public\storage -Force

# 2. Tạo lại
php artisan storage:link
```

**Nếu gặp lỗi "EBUSY: resource busy or locked":**

Lỗi này xảy ra khi thư mục đang được sử dụng bởi chương trình khác.

**Cách fix:**

1. **Đóng các chương trình:**
   - Đóng VS Code
   - Đóng File Explorer đang mở thư mục `public/storage`
   - Đóng web browser
   - Dừng Apache/XAMPP (nếu đang chạy)

2. **Chạy script an toàn:**
   ```powershell
   .\fix_storage_safe.ps1
   ```

3. **Hoặc đổi tên thay vì xóa (an toàn hơn):**
   ```powershell
   Rename-Item public\storage public\storage_old
   php artisan storage:link
   ```

4. **Nếu vẫn lỗi, chạy với quyền Administrator:**
   - Click chuột phải vào PowerShell
   - Chọn "Run as Administrator"
   - Chạy:
     ```powershell
     cd C:\xampp\htdocs\linh\homestay
     Remove-Item public\storage -Force -Recurse
     php artisan storage:link
     ```

5. **Hoặc restart máy tính** rồi chạy lại `php artisan storage:link`

## Nếu vẫn không được

1. **Chạy PowerShell với quyền Administrator:**
   - Click chuột phải vào PowerShell
   - Chọn "Run as Administrator"
   - Chạy lại `.\fix_storage.ps1`

2. **Kiểm tra quyền ghi:**
   ```powershell
   # Test quyền ghi
   "test" | Out-File storage\app\public\test.txt
   # Nếu lỗi, cấp quyền:
   icacls storage /grant Users:F /T
   ```

3. **Xem logs:**
   - Mở file `storage/logs/laravel.log`
   - Tìm lỗi liên quan đến upload

## Lưu ý

- **Luôn chạy `php artisan storage:link`** khi setup project trên máy mới
- Symbolic link phải trỏ đúng đến `storage/app/public`
- Không xóa thư mục `storage/app/public` (chứa hình ảnh đã upload)

