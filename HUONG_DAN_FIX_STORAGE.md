# Hướng dẫn sửa lỗi upload ảnh trên máy khác

## Vấn đề
Ảnh upload được trên máy bạn nhưng không hiển thị trên máy người khác.

## Nguyên nhân
- File được lưu vào `storage/app/public` nhưng không được copy sang `public/storage`
- Permissions không đúng cho thư mục `public/storage`

## Giải pháp

### Cách 1: Chạy script PowerShell (Windows)
```powershell
# Chạy PowerShell với quyền Administrator
.\fix_storage_permissions.ps1
```

### Cách 2: Thủ công
1. Đảm bảo thư mục `public/storage` tồn tại và có quyền ghi
2. Chạy lệnh:
```bash
php artisan storage:link
```

### Cách 3: Copy thủ công
Nếu symbolic link không hoạt động, copy toàn bộ file từ `storage/app/public` sang `public/storage`:
```bash
# Windows
xcopy /E /I storage\app\public public\storage

# Linux/Mac
cp -r storage/app/public/* public/storage/
```

## Kiểm tra
Sau khi fix, kiểm tra:
1. Upload một ảnh mới
2. Kiểm tra xem file có tồn tại trong cả 2 nơi:
   - `storage/app/public/[path]`
   - `public/storage/[path]`
3. Truy cập URL: `http://your-domain/storage/[path]` để xem ảnh

## Lưu ý
- Hệ thống đã được cấu hình để tự động copy file vào `public/storage` khi upload
- Nếu vẫn không được, kiểm tra permissions của thư mục `public/storage`

