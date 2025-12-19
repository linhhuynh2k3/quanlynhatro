## Công việc còn lại trong dự án homestay (Laravel + PostgreSQL)

### 1. Authentication & phân quyền 3 vai trò
- **Cài đặt gói auth** (gợi ý: Laravel Breeze với Blade hoặc Inertia).
- **Tuỳ biến form đăng ký** để chọn vai trò: `landlord` (Chủ trọ) hoặc `tenant` (Người tìm phòng).
- **Thiết lập middleware `role`** để giới hạn truy cập theo vai trò (`admin`, `landlord`, `tenant`).
- **Trang / logic đổi mật khẩu** cho người dùng (đặc biệt Chủ trọ).

### 2. Controllers & Routes cho từng tác nhân

#### 2.1. Admin (Quản lý)
- **Routes nhóm `admin`** (prefix `admin`, middleware `auth`, `role:admin`).
- **Controllers chính**:
  - Quản lý người dùng (`Admin/UserController`): danh sách, khoá/mở khoá, phân quyền.
  - Quản lý bài đăng (`Admin/AdminListingController`): duyệt bài, hủy bài, cho hết hạn, xem chi tiết.
  - Quản lý danh mục (`Admin/CategoryController`): CRUD danh mục cha/con.
  - Quản lý slider (`Admin/SliderController`): CRUD slider, sắp xếp, bật/tắt.
  - Quản lý thông tin cấu hình website (title, hotline, địa chỉ \- nếu cần một bảng config riêng).
  - Quản lý thanh toán (`Admin/PaymentController`): xem lịch sử thanh toán/nạp tiền, thống kê doanh thu.
  - Quản lý tin tức (`Admin/NewsController`): CRUD bài viết tin tức.
  - Quản lý phản hồi/liên hệ (`Admin/FeedbackController`): đánh dấu đã xử lý, ghi chú.
- **Màn hình dashboard admin**: tổng quan doanh thu, số tin đang hoạt động, số user, số phản hồi chưa xử lý, biểu đồ.

#### 2.2. Chủ trọ (Người cho thuê)
- **Routes nhóm `landlord`** (prefix `landlord`, middleware `auth`, `role:landlord`).
- **Controllers chính**:
  - `Landlord/LandlordListingController`:
    - Đăng bài mới (create store).
    - Sửa bài (edit update).
    - Gia hạn bài đăng (update `expired_at`, trừ tiền trong `balance` nếu có phí).
    - Xem danh sách bài đăng của chính chủ trọ, lọc theo trạng thái.
  - `Landlord/LandlordPaymentController`:
    - Nạp tiền vào tài khoản (tạo `Payment` type `deposit`, tăng `users.balance`).
    - Thanh toán bài đăng (tạo `Payment` type `listing_payment`, trừ `balance`).
    - Xem lịch sử thanh toán, nạp tiền.
  - `Landlord/ProfileController`:
    - Xem + cập nhật thông tin cá nhân.
    - Đổi mật khẩu (nếu không dùng sẵn form của gói auth).
  - (Tuỳ chọn) Quản lý hợp đồng với người thuê (`Landlord/ContractController` dùng bảng `contracts`).

#### 2.3. Người tìm phòng
- **Routes frontend** (không prefix hoặc `prefix:site`, middleware `web`).
- **Controllers chính**:
  - `Frontend/ListingController`:
    - Trang danh sách bài đăng (phân trang, lọc theo tỉnh/thành, giá, diện tích, từ khoá).
    - Xem chi tiết bài đăng (tăng `views`).
  - `Frontend/SearchController` (hoặc logic trong `ListingController`):
    - Xử lý form tìm kiếm nâng cao.
  - `Frontend/BookingController` hoặc `Frontend/ContractController`:
    - Đặt thuê (tạo bản ghi `contracts` ở trạng thái `pending`).
    - Ký hợp đồng (chuyển trạng thái `active`, lưu thông tin giá, ngày bắt đầu/kết thúc).
  - `Frontend/CommentController`:
    - Thêm bình luận, trả lời bình luận (yêu cầu đăng nhập).
    - (Tuỳ chọn) Cho phép xoá/sửa bình luận cá nhân.
  - `Frontend/FeedbackController`:
    - Form “Liên hệ / Gửi thắc mắc” lưu vào bảng `feedback`.

### 3. Giao diện người dùng (UI/UX)

#### 3.1. Layout chung
- Tạo layout chính `resources/views/layouts/app.blade.php`:
  - Header với logo, menu (Phòng trọ, Nhà nguyên căn, Căn hộ, Mặt bằng, Tin tức...).
  - Tài khoản (Đăng nhập / Đăng ký / Tài khoản cá nhân / Đăng xuất).
  - Footer với thông tin công ty, hotline, liên kết nhanh giống `phongtro123.com`.

#### 3.2. Trang danh sách bài đăng (giống `https://phongtro123.com/cho-thue-mat-bang`)
- Thanh bộ lọc phía trên:
  - Tỉnh/Thành phố, Quận/Huyện, Phường/Xã.
  - Giá (khoảng giá), Diện tích (khoảng diện tích).
  - Từ khoá tiêu đề/địa chỉ.
- Lưới danh sách bài đăng:
  - Ảnh, tiêu đề, giá, diện tích, địa chỉ, thông tin nhanh.
  - Badge “Nổi bật”, “Mới đăng” nếu là `is_featured` hoặc mới `created_at`.
- Sidebar đề xuất:
  - Tin nổi bật (lọc `is_featured = true`).
  - Danh mục liên quan.

#### 3.3. Trang chi tiết bài đăng
- Hiển thị:
  - Thông tin chi tiết bài đăng (ảnh, mô tả, giá, diện tích, địa chỉ, tiện ích... nếu thêm trường).
  - Thông tin liên hệ chủ trọ (tên, số điện thoại).
  - Nút “Đặt thuê / Liên hệ”.
- Khu vực bình luận:
  - Danh sách bình luận + trả lời.
  - Form gửi bình luận (yêu cầu đăng nhập).

#### 3.4. Giao diện Admin
- Layout admin riêng (sidebar, navbar, breadcrumbs).
- Các trang:
  - Dashboard.
  - Quản lý user.
  - Quản lý bài đăng (danh sách, bộ lọc trạng thái, nút duyệt/hủy, xem chi tiết).
  - Quản lý danh mục, slider, tin tức, thanh toán, phản hồi.

### 4. Nghiệp vụ thanh toán & số dư
- Luồng **nạp tiền**:
  - Form nhập số tiền, tạo bản ghi `payments` type `deposit`, status `success` (tạm thời, chưa tích hợp cổng).
  - Cập nhật `users.balance += amount` khi `status = success`.
- Luồng **thanh toán bài đăng**:
  - Trước khi đăng hoặc gia hạn, kiểm tra `users.balance >= fee`.
  - Nếu đủ:
    - Tạo `payments` type `listing_payment`, status `success`, liên kết `listing_id`.
    - Trừ tiền khỏi `balance`, cập nhật `listings.expired_at`.
- Trang **thống kê doanh thu** cho admin:
  - Tổng tiền nạp, tổng tiền đã dùng cho bài đăng, biểu đồ theo ngày/tháng.

### 5. Kiểm thử & bảo mật cơ bản
- Thêm **validation** cho các form (đăng bài, bình luận, liên hệ, hợp đồng, nạp tiền).
- Kiểm tra **phân quyền** (user chỉ sửa/xoá được tài nguyên của chính mình).
- Ẩn thông tin nhạy cảm (ví dụ: email/số điện thoại chỉ hiện rõ khi đã đăng nhập).


