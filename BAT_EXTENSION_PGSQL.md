# ✅ Đã bật PostgreSQL Extension trong PHP

## Đã thực hiện

Đã bỏ comment (bật) các extension PostgreSQL trong file `C:\xampp\php\php.ini`:

```ini
extension=pdo_pgsql
extension=pgsql
```

## Bước tiếp theo: Restart Apache

**Quan trọng:** Bạn cần restart Apache trong XAMPP để PHP load extension mới.

### Cách 1: Qua XAMPP Control Panel
1. Mở XAMPP Control Panel
2. Click **Stop** cho Apache
3. Click **Start** lại cho Apache

### Cách 2: Qua Services
```powershell
Restart-Service -Name "Apache*"
```

### Cách 3: Nếu dùng `php artisan serve`
- Chỉ cần dừng và chạy lại:
```powershell
# Dừng server hiện tại (Ctrl+C)
# Sau đó chạy lại:
php artisan serve
```

## Kiểm tra extension đã được load

Sau khi restart, chạy lệnh này để kiểm tra:

```powershell
php -m | Select-String -Pattern "pdo_pgsql|pgsql"
```

Nếu thấy `pdo_pgsql` và `pgsql` trong danh sách, extension đã được load thành công! ✅

## Sau đó chạy migrations

```powershell
cd C:\xampp\htdocs\linh\homestay
php artisan migrate
```

## Lưu ý

- Nếu vẫn gặp lỗi, đảm bảo:
  1. Apache đã được restart
  2. PostgreSQL service đang chạy
  3. Database `homestay_db` đã được tạo
  4. Thông tin trong `.env` đúng

