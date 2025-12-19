# Hướng dẫn tạo dữ liệu mẫu

## ✅ Đã hoàn thành

Seeder đã được tạo và chạy thành công! Dữ liệu mẫu đã được thêm vào database.

## 📋 Dữ liệu đã được tạo

### 👥 Users (6 người dùng)
- **Admin:** admin@homestay.com / password
- **Chủ trọ 1:** landlord1@homestay.com / password (số dư: 5,000,000 VNĐ)
- **Chủ trọ 2:** landlord2@homestay.com / password (số dư: 3,000,000 VNĐ)
- **Chủ trọ 3:** landlord3@homestay.com / password (số dư: 2,000,000 VNĐ)
- **Người tìm phòng 1:** tenant1@homestay.com / password
- **Người tìm phòng 2:** tenant2@homestay.com / password

### 📁 Categories (10 danh mục)
- Phòng trọ (và 3 danh mục con)
- Nhà nguyên căn (và 1 danh mục con)
- Căn hộ (và 2 danh mục con)
- Mặt bằng

### 🏠 Listings (8 bài đăng)
- 6 bài đăng đã được duyệt (approved)
- 1 bài đăng chờ duyệt (pending)
- 1 bài đăng nhà nguyên căn
- Một số bài đăng nổi bật (featured)

### 🖼️ Sliders (3 slider)
- 3 slider cho trang chủ

### 📰 News (3 tin tức)
- 3 bài tin tức mẫu

## 🖼️ Thêm hình ảnh

### Cách 1: Tải ảnh từ internet

1. Tạo các thư mục (đã có sẵn):
   ```
   storage/app/public/listings/
   storage/app/public/sliders/
   storage/app/public/news/
   ```

2. Tải ảnh và đặt tên:
   - **Listings:** 1.jpg, 2.jpg, 3.jpg, ... 20.jpg (20 ảnh)
   - **Sliders:** 1.jpg, 2.jpg, 3.jpg (3 ảnh)
   - **News:** 1.jpg, 2.jpg, 3.jpg (3 ảnh)

3. Đảm bảo đã tạo symbolic link:
   ```powershell
   php artisan storage:link
   ```

### Cách 2: Sử dụng ảnh placeholder online

Bạn có thể sử dụng dịch vụ placeholder và lưu ảnh:
- https://via.placeholder.com/800x600.jpg?text=Room+1
- https://via.placeholder.com/1920x600.jpg?text=Slider+1
- https://via.placeholder.com/800x450.jpg?text=News+1

### Cách 3: Tạo ảnh placeholder đơn giản

Chạy script PowerShell:
```powershell
.\tao_anh_mau.ps1
```

Sau đó tải ảnh thủ công hoặc sử dụng ảnh có sẵn.

## 🔄 Chạy lại seeder

Nếu muốn reset và tạo lại dữ liệu:

```powershell
# Reset database và chạy lại seeder
php artisan migrate:fresh --seed

# Hoặc chỉ chạy seeder (nếu đã có dữ liệu, sẽ bị trùng)
php artisan db:seed
```

## 📝 Lưu ý

1. **Hình ảnh:** Nếu không có ảnh, website vẫn chạy được nhưng sẽ không hiển thị ảnh
2. **Mật khẩu:** Tất cả tài khoản mẫu đều dùng mật khẩu: `password`
3. **Dữ liệu:** Dữ liệu mẫu chỉ để test, bạn có thể xóa và thêm dữ liệu thật sau

## 🚀 Sử dụng

1. Đăng nhập với tài khoản admin: admin@homestay.com / password
2. Hoặc đăng nhập với tài khoản chủ trọ: landlord1@homestay.com / password
3. Xem các bài đăng mẫu tại trang danh sách
4. Test các chức năng với dữ liệu mẫu

## 📸 Kích thước ảnh khuyến nghị

- **Listings:** 800x600px (tỷ lệ 4:3)
- **Sliders:** 1920x600px (tỷ lệ 16:5)
- **News:** 800x450px (tỷ lệ 16:9)

