# Hướng dẫn sử dụng hệ thống kiểm duyệt nội dung (Content Moderation)

## Tổng quan

Hệ thống kiểm duyệt tự động đã được tích hợp vào các chức năng:
- ✅ Bình luận (Comments)
- ✅ Bài đăng cho thuê (Listings)
- ✅ Tin nhắn chat (Messages)

## Cấu hình

### 1. Cấu hình cơ bản (trong file `.env`)

```env
# Bật/tắt kiểm duyệt (mặc định: true)
MODERATION_ENABLED=true

# Sử dụng OpenAI Moderation API (mặc định: false)
# Nếu bật, cần thêm OPENAI_API_KEY
MODERATION_USE_OPENAI=false

# API Key của OpenAI (nếu sử dụng OpenAI)
OPENAI_API_KEY=sk-...

# Threshold cho OpenAI (0.0 - 1.0, mặc định: 0.5)
MODERATION_OPENAI_THRESHOLD=0.5

# Hành động khi phát hiện vi phạm:
# - reject: Từ chối ngay lập tức
# - flag: Đánh dấu và chờ duyệt
# - warn: Cảnh báo nhưng vẫn cho phép
MODERATION_ACTION=reject

# Gửi email thông báo cho admin khi phát hiện vi phạm
MODERATION_NOTIFY_ADMIN=false
```

### 2. Cấu hình blacklist từ khóa

File cấu hình: `config/moderation.php`

Bạn có thể thêm/sửa danh sách từ khóa nhạy cảm trong mảng `blacklist_words`.

## Cách hoạt động

### 1. Kiểm tra Blacklist (Luôn hoạt động)

Hệ thống sẽ kiểm tra nội dung với danh sách từ khóa nhạy cảm đã được định nghĩa sẵn.

### 2. Kiểm tra Spam Patterns

Tự động phát hiện:
- Nhiều URL trong text ngắn
- Nhiều số điện thoại trong text ngắn
- Ký tự lặp lại nhiều lần
- Chữ in hoa quá nhiều (SHOUTING)

### 3. OpenAI Moderation API (Tùy chọn)

Nếu bật `MODERATION_USE_OPENAI=true` và có `OPENAI_API_KEY`, hệ thống sẽ:
- Gửi nội dung đến OpenAI Moderation API
- Kiểm tra các category: hate, harassment, self-harm, sexual, violence
- Trả về kết quả với score và category

**Lưu ý**: OpenAI Moderation API có free tier, nhưng có giới hạn. Xem thêm tại: https://platform.openai.com/docs/guides/moderation

## Cách sử dụng trong code

### Kiểm tra text đơn giản:

```php
use App\Services\ContentModerationService;

$moderationService = app(ContentModerationService::class);
$result = $moderationService->checkText('Nội dung cần kiểm tra');

if ($result['is_violated']) {
    // Xử lý vi phạm
    $reason = $result['reason'];
    $violations = $result['violations'];
}
```

### Kiểm tra listing (title + description):

```php
$result = $moderationService->checkListing($title, $description);
```

## Các điểm đã tích hợp

1. **Comments** (`CommentController`):
   - Kiểm tra khi tạo comment mới
   - Kiểm tra khi reply comment

2. **Listings** (`LandlordListingController`):
   - Kiểm tra khi tạo listing mới
   - Kiểm tra khi cập nhật listing

3. **Chat Messages** (`ChatController`):
   - Kiểm tra khi gửi tin nhắn

## Tùy chỉnh

### Thêm từ khóa mới vào blacklist:

Sửa file `config/moderation.php`:

```php
'blacklist_words' => [
    // ... các từ khóa hiện có
    'từ khóa mới 1',
    'từ khóa mới 2',
],
```

Sau đó chạy:
```bash
php artisan config:clear
```

### Thay đổi hành động khi vi phạm:

Trong `.env`:
```env
MODERATION_ACTION=flag  # Thay vì reject, sẽ đánh dấu và chờ duyệt
```

## Lưu ý

1. **OpenAI API**: Cần có API key và có thể phát sinh chi phí nếu vượt free tier
2. **Performance**: Kiểm tra blacklist rất nhanh, nhưng OpenAI API có thể mất vài giây
3. **Privacy**: Nếu dùng OpenAI, nội dung sẽ được gửi đến server của OpenAI
4. **False Positives**: Có thể có trường hợp báo sai, cần điều chỉnh threshold hoặc blacklist

## Troubleshooting

### Kiểm duyệt không hoạt động:

1. Kiểm tra `MODERATION_ENABLED=true` trong `.env`
2. Chạy `php artisan config:clear`
3. Kiểm tra log: `storage/logs/laravel.log`

### OpenAI API không hoạt động:

1. Kiểm tra `OPENAI_API_KEY` trong `.env`
2. Kiểm tra `MODERATION_USE_OPENAI=true`
3. Kiểm tra kết nối internet
4. Xem log để biết lỗi cụ thể

## Tài liệu tham khảo

- OpenAI Moderation API: https://platform.openai.com/docs/guides/moderation
- Laravel Config: https://laravel.com/docs/configuration

