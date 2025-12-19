# Homestay — Tổng quan dự án

---

# Slide 1 — Tiêu đề
- Tên dự án: Homestay (Laravel)
- Mục tiêu: Nền tảng đăng và tìm phòng trọ, cho thuê ngắn/dài hạn
- Đơn vị phát triển: mã nguồn trong `c:/xampp/htdocs/homestay/homestay`

---

# Slide 2 — Tổng quan chức năng
- Quản lý listings (tạo, chỉnh sửa, duyệt)
- Hệ thống user: landlord (chủ nhà) và tenant (người tìm phòng)
- Tìm kiếm theo vị trí và bán kính
- Thanh toán tích hợp (VNPAY)
- Hóa đơn và quản lý hợp đồng
- Thông báo, feedback, và dashboard admin/landlord

---

# Slide 3 — Các trang/flow chính (user)
- Đăng ký / Đăng nhập (bao gồm đăng ký chủ nhà)
- Chủ nhà: Tạo bài đăng, quản lý phòng, thanh toán, hóa đơn
- Người tìm phòng: Tìm kiếm, lọc (giá, diện tích, vị trí), đặt/thuê
- Admin: Duyệt tin, thống kê, quản lý doanh thu

---

# Slide 4 — Tính năng bản đồ & vị trí
- Thư viện: `Leaflet.js` + OpenStreetMap tiles
- Reverse geocoding: Nominatim (OpenStreetMap)
- Người dùng click trên bản đồ để chọn vị trí (lat/lng)
- Tọa độ lưu trong `latitude`/`longitude` trên `listings`
- Mã liên quan: `resources/views/*/listings/*.blade.php` (create/edit), `resources/views/auth/landlord-register.blade.php`

---

# Slide 5 — Tìm kiếm theo khoảng cách (kỹ thuật)
- Công thức Haversine dùng trên server để lọc/sắp xếp:
  - SQL dùng `whereRaw` và `selectRaw` với biểu thức:
    - 6371 * acos( cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)) )
- File chính: `app/Http/Controllers/Frontend/ListingController.php`

---

# Slide 6 — Mô hình dữ liệu chính
- Bảng `listings`: chứa `latitude`, `longitude`, `price`, `area`, `total_units`, `available_units`, `address`, `status`, ...
- Bảng `users`: role (`landlord`, `tenant`), `balance`
- Bảng `payments`, `invoices`, `contracts`
- Migration / seeders có trong `database/migrations` và `database/seeders`

---

# Slide 7 — Thanh toán & bảo mật giao dịch
- Cổng thanh toán: VNPAY (config tại `config/vnpay.php`)
- Xác thực dữ liệu giao dịch bằng HMAC-SHA512:
  - `hash_hmac('sha512', implode('&', $hashPieces), config('vnpay.hash_secret'))`
- Controller xử lý: `app/Http/Controllers/Landlord/LandlordPaymentController.php`

---

# Slide 8 — Tính toán giá & nghiệp vụ liên quan
- Giá đăng tin (helper): `app/Helpers/ListingPriceHelper.php`
  - Giá theo ngày/tuần/tháng (2000, 12000, 48000 VNĐ)
  - Tối thiểu 5 ngày
- Hóa đơn: tính điện/nước = (mới - cũ) * đơn giá
- Rút tiền: phí % và giá trị tối thiểu (view: withdrawals)

---

# Slide 9 — Kiến trúc kỹ thuật & stack
- Backend: Laravel (PHP)
- DB: PostgreSQL (có file hướng dẫn cấu hình `CAU_HINH_PGSQL.md`)
- Frontend: Blade templates, Tailwind / Bootstrap, Vite
- Bản đồ: Leaflet + OpenStreetMap
- Tests: PHPUnit (có `phpunit.xml` và thư mục `tests/`)
- Dependencies: Composer + npm (vite)

---

# Slide 10 — Tệp và vị trí quan trọng
- `app/Http/Controllers/Frontend/ListingController.php` — tìm kiếm & Haversine
- `app/Helpers/ListingPriceHelper.php` — tính giá đăng tin
- `resources/views/landlord/listings/create.blade.php` — map & chọn vị trí
- `resources/views/frontend/listings/index.blade.php` — modal chọn vị trí, tìm theo radius
- `config/vnpay.php`, `app/Http/Controllers/Landlord/LandlordPaymentController.php` — VNPAY

---

# Slide 11 — Chạy project (tổng quan)
- Thiết lập môi trường:
  - `cp .env.example .env` → chỉnh `DB_*`, `APP_URL`, `VNPAY` keys
  - `composer install`
  - `php artisan key:generate`
  - `php artisan migrate --seed`
  - `npm install` && `npm run dev`
  - Chạy server: `php artisan serve` hoặc cấu hình qua XAMPP
- Lưu ý: có hướng dẫn cài đặt trong nhiều file `HUONG_DAN_*.md`

---

# Slide 12 — Điểm cần chú ý / cải tiến gợi ý
- Rate-limit / caching cho API Nominatim (hạn chế bị chặn)
- Thêm geospatial index (PostGIS) nếu cần truy vấn khoảng cách lớn/nhanh
- Giới hạn và validate input lat/lng ở client + server
- Thêm tests cho search-by-distance và tính toán giá
- Tài liệu hóa API nội bộ (endpoints, params)

---

# Slide 13 — Tóm tắt & Next steps
- Dự án: nền tảng đăng/tìm phòng đầy đủ tính năng (map, thanh toán, hóa đơn)
- Tôi đã tạo file Markdown slides: `presentation.md` (nằm tại root dự án)
- Next steps:
  - Xuất Markdown → PPTX (ví dụ dùng `pandoc` hoặc dịch vụ):
    - `pandoc presentation.md -t pptx -o Homestay-presentation.pptx`
  - Kiểm tra/cập nhật thông tin chi tiết (số liệu, screenshots)

---

# Slide 14 — Liên hệ & ghi chú
- Đường dẫn repo: local workspace `c:/xampp/htdocs/homestay/homestay`
- Tài liệu hỗ trợ: `README.md`, `HUONG_DAN_*.md`
- Nếu muốn, tôi có thể:
  - Xuất file PPTX từ Markdown
  - Thêm các slide có screenshot UI
  - Làm slide tiếng Anh
