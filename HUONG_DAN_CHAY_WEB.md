# Hướng dẫn chạy web Homestay (Laravel + PostgreSQL)

## Yêu cầu hệ thống

Trước khi bắt đầu, đảm bảo bạn đã cài đặt:

1. **PHP 8.2 trở lên** (kiểm tra: `php -v`)
2. **Composer** (kiểm tra: `composer --version`)
3. **Node.js và npm** (kiểm tra: `node -v` và `npm -v`)
4. **PostgreSQL** (kiểm tra: `psql --version`) - **BẮT BUỘC**
5. **XAMPP** (đã cài đặt theo workspace path)

## Các bước chạy web

### Bước 1: Kiểm tra và cấu hình cơ sở dữ liệu PostgreSQL

**Dự án đã được cấu hình để sử dụng PostgreSQL hoàn toàn.**

1. **Khởi động PostgreSQL** (nếu chưa chạy):
   - Mở pgAdmin hoặc kiểm tra service PostgreSQL đang chạy
   - Hoặc mở Services (services.msc) và tìm PostgreSQL service

2. **Tạo database mới**:

   **Cách 1: Sử dụng script tự động (Khuyến nghị)**
   ```powershell
   cd C:\xampp\htdocs\linh\homestay
   .\tao_database_pgsql.ps1
   ```

   **Cách 2: Tạo thủ công bằng SQL**
   - Mở pgAdmin hoặc psql và chạy:
   ```sql
   CREATE DATABASE homestay_db;
   ```

   **Cách 3: Sử dụng psql command line**
   ```powershell
   psql -U postgres -c "CREATE DATABASE homestay_db;"
   ```

3. **Kiểm tra file `.env`** đã được cấu hình đúng:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=homestay_db
   DB_USERNAME=postgres
   DB_PASSWORD=tranduc123
   ```
   ⚠️ **Lưu ý:** Thay `tranduc123` bằng mật khẩu PostgreSQL thực tế của bạn nếu khác.

### Bước 2: Cài đặt dependencies (nếu chưa cài)

Mở PowerShell hoặc Command Prompt, di chuyển vào thư mục dự án:

```powershell
cd C:\xampp\htdocs\linh\homestay
```

**Cài đặt PHP dependencies:**
```powershell
composer install
```

**Cài đặt Node.js dependencies:**
```powershell
npm install
```

### Bước 3: Cấu hình ứng dụng

1. **Tạo Application Key** (nếu chưa có):
   ```powershell
   php artisan key:generate
   ```

2. **Chạy migrations** (tạo các bảng trong database):
   ```powershell
   php artisan migrate
   ```

3. **Tạo symbolic link cho storage** (để hiển thị ảnh):
   ```powershell
   php artisan storage:link
   ```

### Bước 4: Build assets (CSS/JS)

**Cho môi trường development:**
```powershell
npm run dev
```
(Lệnh này sẽ chạy Vite dev server - giữ terminal này mở)

**Hoặc build cho production:**
```powershell
npm run build
```

### Bước 5: Khởi động web server

Mở một terminal mới (giữ terminal `npm run dev` đang chạy) và chạy:

```powershell
cd C:\xampp\htdocs\linh\homestay
php artisan serve
```

Web sẽ chạy tại: **http://127.0.0.1:8000** hoặc **http://localhost:8000**

### Bước 6: Truy cập web

Mở trình duyệt và truy cập:
- **Trang chủ:** http://localhost:8000
- **Đăng ký/Đăng nhập:** http://localhost:8000/register hoặc http://localhost:8000/login

## Lưu ý quan trọng

### Nếu gặp lỗi về database:

1. **Kiểm tra PostgreSQL đang chạy:**
   - Mở Services (services.msc) và tìm PostgreSQL
   - Đảm bảo service đang chạy
   - Hoặc kiểm tra bằng lệnh:
   ```powershell
   Get-Service -Name "*postgresql*"
   ```

2. **Kiểm tra database đã được tạo:**
   ```powershell
   psql -U postgres -l | findstr homestay_db
   ```
   Nếu không thấy, chạy lại script `tao_database_pgsql.ps1` hoặc tạo thủ công.

3. **Kiểm tra thông tin kết nối trong `.env`:**
   - Đảm bảo `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` đúng
   - Đặc biệt chú ý `DB_PASSWORD` phải khớp với mật khẩu PostgreSQL của bạn

4. **Test kết nối database:**
   ```powershell
   php artisan tinker
   ```
   Sau đó gõ:
   ```php
   DB::connection()->getPdo();
   ```
   Nếu không lỗi thì kết nối thành công.

5. **Kiểm tra quyền của user:**
   - Đảm bảo user `postgres` (hoặc user bạn dùng) có quyền tạo và truy cập database

### Nếu gặp lỗi về permissions:

1. **Quyền ghi cho thư mục storage và bootstrap/cache:**
   ```powershell
   # Windows thường không cần, nhưng nếu cần:
   icacls storage /grant Users:F /T
   icacls bootstrap\cache /grant Users:F /T
   ```

### Nếu gặp lỗi về Vite/assets:

1. **Xóa cache và build lại:**
   ```powershell
   npm run build
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

### Nếu gặp lỗi upload hình ảnh (tin tức, phòng trọ):

**Triệu chứng:** Upload hình trên máy bạn được, nhưng trên máy người khác không được.

**Nguyên nhân:** Thiếu symbolic link từ `public/storage` đến `storage/app/public`

**Cách fix:**

**Cách 1: Sử dụng script tự động (Khuyến nghị)**
```powershell
cd C:\xampp\htdocs\linh\homestay
.\fix_storage.ps1
```

**Cách 2: Fix thủ công**
```powershell
# 1. Xóa symbolic link cũ (nếu có)
Remove-Item public\storage -Force -ErrorAction SilentlyContinue

# 2. Tạo symbolic link mới
php artisan storage:link

# 3. Kiểm tra thư mục storage/app/public có tồn tại không
# Nếu chưa có, tạo thủ công:
New-Item -ItemType Directory -Path storage\app\public\listings -Force
New-Item -ItemType Directory -Path storage\app\public\news -Force
New-Item -ItemType Directory -Path storage\app\public\sliders -Force

# 4. Xóa cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

**Kiểm tra sau khi fix:**
1. Mở thư mục `public/storage` - phải là symbolic link (có mũi tên nhỏ)
2. Thử upload hình ảnh trong admin panel
3. Kiểm tra hình có hiển thị không (URL sẽ là `/storage/listings/...` hoặc `/storage/news/...`)

**Lưu ý:** Nếu vẫn không được, chạy PowerShell với quyền Administrator và chạy lại script `fix_storage.ps1`

## Chạy tất cả cùng lúc (Development)

Bạn có thể sử dụng script có sẵn trong `composer.json`:

```powershell
composer run dev
```

Lệnh này sẽ tự động chạy:
- PHP development server
- Queue worker
- Log viewer (Pail)
- Vite dev server

## Tạo dữ liệu mẫu (Seeders)

Nếu có seeder, chạy:
```powershell
php artisan db:seed
```

## Các lệnh hữu ích khác

- **Xem danh sách routes:** `php artisan route:list`
- **Xóa cache:** `php artisan cache:clear`
- **Xóa config cache:** `php artisan config:clear`
- **Xem logs:** Xem file `storage/logs/laravel.log`

## Cấu trúc thư mục quan trọng

- `app/` - Code PHP chính
- `resources/views/` - Templates Blade
- `routes/web.php` - Định nghĩa routes
- `database/migrations/` - Migrations database
- `public/` - Thư mục public (document root)
- `.env` - File cấu hình môi trường

## Troubleshooting

### Lỗi "Class not found"
```powershell
composer dump-autoload
```

### Lỗi "500 Internal Server Error"
- Kiểm tra file `.env` có đúng không
- Kiểm tra logs trong `storage/logs/laravel.log`
- Chạy `php artisan config:clear`

### Lỗi "419 Page Expired" (CSRF)
- Xóa cookies trình duyệt
- Kiểm tra `APP_KEY` trong `.env`

---

**Chúc bạn code vui vẻ! 🚀**

