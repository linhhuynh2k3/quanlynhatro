# ✅ Đã chuyển dự án sang PostgreSQL

## Cấu hình hiện tại

Dự án đã được cấu hình để sử dụng **PostgreSQL** hoàn toàn.

### File `.env` đã được cập nhật:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=homestay_db
DB_USERNAME=postgres
DB_PASSWORD=linh
```

### File `config/database.php`:

- Default connection: `pgsql` ✅

## Các bước tiếp theo

### 1. Tạo database PostgreSQL

Chạy script tự động:
```powershell
.\tao_database_pgsql.ps1
```

Hoặc tạo thủ công:
```sql
CREATE DATABASE homestay_db;
```

### 2. Chạy migrations

```powershell
php artisan migrate
```

### 3. (Tùy chọn) Chạy seeders

```powershell
php artisan db:seed
```

### 4. Xóa cache config

```powershell
php artisan config:clear
php artisan cache:clear
```

## Kiểm tra kết nối

```powershell
php artisan tinker
```

Trong tinker, chạy:
```php
DB::connection()->getPdo();
```

Nếu không có lỗi, kết nối thành công! ✅

## Lưu ý

- ⚠️ Đảm bảo PostgreSQL service đang chạy
- ⚠️ Kiểm tra mật khẩu trong `.env` khớp với PostgreSQL của bạn
- ⚠️ User `postgres` phải có quyền tạo database

## Xem hướng dẫn chi tiết

Xem file `HUONG_DAN_CHAY_WEB.md` để biết thêm chi tiết.

