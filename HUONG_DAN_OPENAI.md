# Hướng dẫn setup OpenAI Moderation API (MIỄN PHÍ)

## 🎯 OpenAI Moderation API là gì?

OpenAI Moderation API là công cụ **HOÀN TOÀN MIỄN PHÍ** để kiểm duyệt nội dung tự động. API này:
- ✅ **KHÔNG tính vào quota** của bạn
- ✅ **KHÔNG tốn tiền** khi sử dụng
- ✅ Chính xác cao, hỗ trợ nhiều ngôn ngữ
- ✅ Dễ tích hợp

---

## 📝 Bước 1: Đăng ký tài khoản OpenAI

1. Truy cập: **https://platform.openai.com/signup**
2. Đăng ký bằng:
   - Email
   - Google Account
   - Microsoft Account
3. Xác thực email (nếu cần)

---

## 🔑 Bước 2: Lấy API Key

1. Đăng nhập vào: **https://platform.openai.com/**
2. Vào mục **"API keys"** (hoặc truy cập trực tiếp: https://platform.openai.com/api-keys)
3. Click nút **"Create new secret key"**
4. Đặt tên cho key (ví dụ: "Homestay Moderation")
5. **Copy API key ngay lập tức** - chỉ hiện 1 lần!
   - Format: `sk-...` (bắt đầu bằng `sk-`)

⚠️ **LƯU Ý QUAN TRỌNG**: 
- API key chỉ hiện 1 lần, hãy copy và lưu lại ngay
- Không chia sẻ API key với ai
- Nếu mất, phải tạo key mới

---

## ⚙️ Bước 3: Cấu hình trong project

### 3.1. Thêm API key vào file `.env`

Mở file `.env` trong thư mục gốc của project và thêm:

```env
# Bật kiểm duyệt
MODERATION_ENABLED=true

# Bật OpenAI Moderation API
MODERATION_USE_OPENAI=true

# API Key của OpenAI (thay YOUR_API_KEY bằng key bạn vừa copy)
OPENAI_API_KEY=sk-YOUR_API_KEY_HERE

# Threshold (0.0 - 1.0), mặc định 0.5
# Số càng cao = càng nghiêm ngặt
MODERATION_OPENAI_THRESHOLD=0.5

# Hành động khi phát hiện vi phạm: reject (từ chối), flag (đánh dấu), warn (cảnh báo)
MODERATION_ACTION=reject
```

**Ví dụ:**
```env
MODERATION_ENABLED=true
MODERATION_USE_OPENAI=true
OPENAI_API_KEY=sk-proj-abc123xyz789...
MODERATION_OPENAI_THRESHOLD=0.5
MODERATION_ACTION=reject
```

### 3.2. Clear cache config

Chạy lệnh sau để Laravel load config mới:

```bash
php artisan config:clear
```

Hoặc nếu dùng Windows:
```cmd
php artisan config:clear
```

---

## ✅ Bước 4: Kiểm tra hoạt động

### Test thử:

1. Tạo một comment/listing với nội dung vi phạm (ví dụ: chứa từ ngữ xúc phạm)
2. Hệ thống sẽ tự động kiểm tra và từ chối
3. Xem log nếu có lỗi: `storage/logs/laravel.log`

### Kiểm tra log:

```bash
# Xem log mới nhất
tail -f storage/logs/laravel.log
```

---

## 🎛️ Cấu hình nâng cao

### Điều chỉnh độ nhạy (Threshold)

Threshold càng cao = càng nghiêm ngặt (ít false positive, nhưng có thể bỏ sót)

```env
# Nghiêm ngặt hơn (ít false positive)
MODERATION_OPENAI_THRESHOLD=0.7

# Dễ dãi hơn (bắt nhiều hơn, nhưng có thể có false positive)
MODERATION_OPENAI_THRESHOLD=0.3
```

### Thay đổi hành động khi vi phạm

```env
# Từ chối ngay (mặc định)
MODERATION_ACTION=reject

# Đánh dấu và chờ admin duyệt
MODERATION_ACTION=flag

# Cảnh báo nhưng vẫn cho phép
MODERATION_ACTION=warn
```

---

## 🔍 Các loại vi phạm OpenAI phát hiện

OpenAI Moderation API phát hiện các loại vi phạm sau:

1. **Hate** - Nội dung kích động thù địch
2. **Hate/Threatening** - Đe dọa
3. **Harassment** - Quấy rối
4. **Harassment/Threatening** - Quấy rối đe dọa
5. **Self-harm** - Tự hại
6. **Self-harm/Intent** - Có ý định tự hại
7. **Self-harm/Instructions** - Hướng dẫn tự hại
8. **Sexual** - Nội dung tình dục
9. **Sexual/Minors** - Nội dung tình dục trẻ em
10. **Violence** - Bạo lực
11. **Violence/Graphic** - Bạo lực đồ họa

---

## ❓ Troubleshooting

### Lỗi: "Invalid API key"

- Kiểm tra API key đúng chưa (phải bắt đầu bằng `sk-`)
- Kiểm tra có copy đầy đủ không (không có khoảng trắng)
- Thử tạo API key mới

### Lỗi: "Insufficient quota"

- OpenAI Moderation API **MIỄN PHÍ**, không có quota
- Nếu gặp lỗi này, có thể do tài khoản chưa được kích hoạt đầy đủ
- Kiểm tra billing settings: https://platform.openai.com/account/billing

### API không hoạt động

1. Kiểm tra internet connection
2. Kiểm tra `MODERATION_USE_OPENAI=true` trong `.env`
3. Chạy `php artisan config:clear`
4. Xem log: `storage/logs/laravel.log`

### Test API key thủ công

Bạn có thể test API key bằng curl:

```bash
curl https://api.openai.com/v1/moderations \
  -H "Authorization: Bearer sk-YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"input": "This is a test"}'
```

Nếu trả về JSON với `"flagged": false` thì API key hoạt động tốt.

---

## 💡 Tips

1. **Kết hợp Blacklist + AI**: Hệ thống sẽ kiểm tra blacklist trước, sau đó mới dùng AI
2. **Monitor log**: Thường xuyên xem log để điều chỉnh threshold
3. **Test trước**: Test với nhiều loại nội dung trước khi deploy production
4. **Backup API key**: Lưu API key ở nơi an toàn

---

## 📚 Tài liệu tham khảo

- OpenAI Moderation API: https://platform.openai.com/docs/guides/moderation
- OpenAI Platform: https://platform.openai.com/
- API Documentation: https://platform.openai.com/docs/api-reference/moderations

---

## ✅ Checklist

- [ ] Đã đăng ký tài khoản OpenAI
- [ ] Đã tạo và copy API key
- [ ] Đã thêm vào file `.env`
- [ ] Đã chạy `php artisan config:clear`
- [ ] Đã test với nội dung vi phạm
- [ ] Đã kiểm tra log không có lỗi

---

**Chúc bạn setup thành công! 🎉**

