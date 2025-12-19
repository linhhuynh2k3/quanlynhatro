# Hướng dẫn sử dụng giao diện mới

## Đã hoàn thành

✅ **Thiết kế lại toàn bộ giao diện frontend với Bootstrap 5**

### Các thay đổi chính:

1. **Layout mới** (`resources/views/layouts/frontend.blade.php`)
   - Header với gradient đẹp
   - Navigation responsive với Bootstrap 5
   - Footer với thông tin đầy đủ
   - Bootstrap Icons cho icon

2. **Trang danh sách** (`resources/views/frontend/listings/index.blade.php`)
   - Hero section với gradient
   - Search box nổi bật
   - Listing cards đẹp với hover effects
   - Sidebar với tin nổi bật và danh mục
   - Responsive hoàn toàn

3. **Trang chi tiết** (`resources/views/frontend/listings/show.blade.php`)
   - Image gallery với carousel
   - Thông tin chi tiết đầy đủ
   - Comments section đẹp
   - Sidebar với liên hệ và bài đăng liên quan

4. **CSS Custom** (`resources/css/custom.css`)
   - Variables CSS cho màu sắc
   - Animations và transitions
   - Responsive design
   - Modern UI components

## Cài đặt

### Bước 1: Cài đặt Bootstrap 5

```powershell
cd C:\xampp\htdocs\linh\homestay
npm install
```

### Bước 2: Build assets

```powershell
npm run build
```

Hoặc chạy dev server:

```powershell
npm run dev
```

### Bước 3: Kiểm tra CSS

File CSS custom đã được copy vào `public/css/custom.css`. Nếu chưa có, chạy:

```powershell
Copy-Item "resources\css\custom.css" -Destination "public\css\custom.css" -Force
```

## Tính năng giao diện

### 1. Header
- Gradient background đẹp
- Sticky navigation
- Responsive menu
- User dropdown menu

### 2. Hero Section
- Gradient background
- Title và subtitle
- Fade-in animation

### 3. Search Box
- Nổi bật với shadow
- Form tìm kiếm đầy đủ
- Responsive grid

### 4. Listing Cards
- Hover effects
- Badge nổi bật/mới
- Image với zoom effect
- Info badges đẹp
- Responsive layout

### 5. Sidebar
- Sticky positioning
- Featured listings
- Categories list
- Quick links

### 6. Detail Page
- Image gallery carousel
- Info grid layout
- Comments với replies
- Booking modal

### 7. Footer
- Gradient background
- 4 columns layout
- Social media links
- Responsive

## Màu sắc chủ đạo

- **Primary**: #e74c3c (Đỏ)
- **Secondary**: #3498db (Xanh dương)
- **Success**: #27ae60 (Xanh lá)
- **Warning**: #f39c12 (Vàng cam)
- **Dark**: #2c3e50 (Xám đậm)

## Responsive Breakpoints

- Mobile: < 768px
- Tablet: 768px - 992px
- Desktop: > 992px

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Lưu ý

1. **Bootstrap 5** được load từ CDN trong layout
2. **Custom CSS** được load từ `public/css/custom.css`
3. **Bootstrap Icons** được load từ CDN
4. Đảm bảo chạy `npm run build` sau khi thay đổi CSS

## Tùy chỉnh

### Thay đổi màu sắc

Sửa file `resources/css/custom.css`:

```css
:root {
    --primary-color: #e74c3c;  /* Thay đổi màu chính */
    --secondary-color: #3498db; /* Thay đổi màu phụ */
    /* ... */
}
```

### Thay đổi font

Sửa trong `resources/views/layouts/frontend.blade.php`:

```html
<link href="https://fonts.googleapis.com/css2?family=YourFont&display=swap" rel="stylesheet">
```

## Troubleshooting

### CSS không load
- Kiểm tra file `public/css/custom.css` có tồn tại
- Clear cache: `php artisan cache:clear`
- Hard refresh browser (Ctrl+F5)

### Bootstrap không hoạt động
- Kiểm tra kết nối internet (CDN)
- Kiểm tra console browser có lỗi không

### Layout bị lỗi
- Kiểm tra Bootstrap 5 đã được load
- Kiểm tra custom CSS không conflict với Bootstrap

---

**Giao diện đã được thiết kế đẹp, hiện đại và responsive! 🎨✨**

