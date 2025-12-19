# Hướng Dẫn: Lưu File Vào Cả 2 Nơi

## Đã Cài Đặt

Tôi đã tạo **StorageHelper** để tự động lưu file vào cả 2 nơi:
- `storage/app/public/` (chuẩn Laravel)
- `public/storage/` (để máy khác không cần symbolic link)

## Cách Hoạt Động

Khi upload file:
1. File được lưu vào `storage/app/public/` (đúng chuẩn)
2. Tự động copy vào `public/storage/` (để hiển thị)
3. Xóa file cũng xóa ở cả 2 nơi

## Đã Cập Nhật

Các controller đã được cập nhật:
- ✅ `NewsController` - Upload ảnh tin tức
- ✅ `LandlordListingController` - Upload ảnh phòng trọ
- ✅ `SliderController` - Upload ảnh slider

## Cài Đặt

**Bước 1: Chạy composer dump-autoload**
```powershell
cd C:\xampp\htdocs\linh\homestay
composer dump-autoload
```

**Bước 2: Đảm bảo thư mục public/storage tồn tại**
```powershell
# Tạo thư mục nếu chưa có
New-Item -ItemType Directory -Path public\storage\listings -Force
New-Item -ItemType Directory -Path public\storage\news -Force
New-Item -ItemType Directory -Path public\storage\sliders -Force
```

**Bước 3: Test upload**
- Thử upload hình ảnh
- Kiểm tra file có trong cả 2 nơi:
  - `storage/app/public/listings/...`
  - `public/storage/listings/...`

## Lưu Ý

⚠️ **Cách này không khuyến nghị** vì:
- Tốn dung lượng (lưu 2 bản)
- Khó đồng bộ (có thể mất đồng bộ)
- Không đúng chuẩn Laravel

✅ **Cách tốt nhất vẫn là:**
- Tạo symbolic link: `php artisan storage:link`
- File chỉ lưu 1 bản trong `storage/app/public/`

## Nếu Muốn Tắt Tính Năng Này

Nếu muốn quay lại cách cũ (chỉ lưu 1 nơi), sửa lại các controller:

**Thay:**
```php
StorageHelper::storeAndCopy($file, 'news');
```

**Bằng:**
```php
$file->store('news', 'public');
```

Và sửa lại phần xóa:
```php
Storage::disk('public')->delete($path);
```


