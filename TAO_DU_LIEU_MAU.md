# Hướng dẫn tạo dữ liệu mẫu

## Bước 1: Tạo thư mục lưu hình ảnh

Tạo các thư mục sau trong `storage/app/public`:

```powershell
# Từ thư mục homestay
mkdir -p storage/app/public/listings
mkdir -p storage/app/public/sliders
mkdir -p storage/app/public/news
```

Hoặc tạo thủ công:
- `storage/app/public/listings/`
- `storage/app/public/sliders/`
- `storage/app/public/news/`

## Bước 2: Thêm hình ảnh mẫu

### Hình ảnh cho Listings (phòng trọ)
Đặt các file ảnh vào `storage/app/public/listings/` với tên:
- `1.jpg`, `2.jpg`, `3.jpg`, ... `20.jpg`

**Gợi ý:** Bạn có thể:
1. Tải ảnh từ internet về (ảnh phòng trọ, nhà trọ)
2. Hoặc tạo ảnh placeholder bằng cách tạo file text với nội dung "Image 1", "Image 2", ...
3. Hoặc sử dụng dịch vụ placeholder như: https://via.placeholder.com/800x600.jpg?text=Room+1

### Hình ảnh cho Sliders
Đặt các file ảnh vào `storage/app/public/sliders/` với tên:
- `1.jpg`, `2.jpg`, `3.jpg`

**Gợi ý:** Ảnh slider nên có kích thước lớn (1920x600px hoặc tương tự)

### Hình ảnh cho News
Đặt các file ảnh vào `storage/app/public/news/` với tên:
- `1.jpg`, `2.jpg`, `3.jpg`

## Bước 3: Tạo symbolic link (nếu chưa có)

```powershell
php artisan storage:link
```

## Bước 4: Chạy seeder

```powershell
php artisan db:seed
```

Hoặc reset và seed lại toàn bộ:

```powershell
php artisan migrate:fresh --seed
```

## Dữ liệu mẫu được tạo

### Users
- **Admin:** admin@homestay.com / password
- **Chủ trọ 1:** landlord1@homestay.com / password (số dư: 5,000,000 VNĐ)
- **Chủ trọ 2:** landlord2@homestay.com / password (số dư: 3,000,000 VNĐ)
- **Chủ trọ 3:** landlord3@homestay.com / password (số dư: 2,000,000 VNĐ)
- **Người tìm phòng 1:** tenant1@homestay.com / password
- **Người tìm phòng 2:** tenant2@homestay.com / password

### Categories
- Phòng trọ (và các danh mục con)
- Nhà nguyên căn
- Căn hộ
- Mặt bằng

### Listings
- 8 bài đăng mẫu với các trạng thái khác nhau
- Một số bài đăng nổi bật (featured)
- Có hình ảnh từ 1.jpg đến 20.jpg

### Sliders
- 3 slider cho trang chủ

### News
- 3 bài tin tức mẫu

## Lưu ý

1. Nếu không có hình ảnh thật, bạn có thể tạo file ảnh placeholder hoặc tải ảnh mẫu từ internet
2. Đảm bảo file ảnh có định dạng .jpg hoặc .png
3. Kích thước ảnh khuyến nghị:
   - Listings: 800x600px
   - Sliders: 1920x600px
   - News: 800x450px

## Script tự động tạo ảnh placeholder (PowerShell)

Tạo file `tao_anh_mau.ps1`:

```powershell
# Tạo thư mục
New-Item -ItemType Directory -Force -Path "storage/app/public/listings"
New-Item -ItemType Directory -Force -Path "storage/app/public/sliders"
New-Item -ItemType Directory -Force -Path "storage/app/public/news"

# Tạo ảnh placeholder cho listings (sử dụng ImageMagick nếu có)
# Hoặc tải từ internet
for ($i=1; $i -le 20; $i++) {
    # Tải ảnh placeholder
    $url = "https://via.placeholder.com/800x600.jpg?text=Room+$i"
    Invoke-WebRequest -Uri $url -OutFile "storage/app/public/listings/$i.jpg"
}

# Tạo ảnh cho sliders
for ($i=1; $i -le 3; $i++) {
    $url = "https://via.placeholder.com/1920x600.jpg?text=Slider+$i"
    Invoke-WebRequest -Uri $url -OutFile "storage/app/public/sliders/$i.jpg"
}

# Tạo ảnh cho news
for ($i=1; $i -le 3; $i++) {
    $url = "https://via.placeholder.com/800x450.jpg?text=News+$i"
    Invoke-WebRequest -Uri $url -OutFile "storage/app/public/news/$i.jpg"
}

Write-Host "Đã tạo xong các ảnh mẫu!"
```

