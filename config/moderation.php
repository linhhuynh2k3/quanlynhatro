<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Content Moderation Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho hệ thống kiểm duyệt nội dung
    |
    */

    // Bật/tắt kiểm duyệt
    'enabled' => env('MODERATION_ENABLED', true),

    // Sử dụng OpenAI Moderation API (MIỄN PHÍ - không tính vào quota)
    'use_openai' => env('MODERATION_USE_OPENAI', false),

    // Blacklist từ khóa (có thể thêm vào .env hoặc database)
    'blacklist_words' => [
        // Từ ngữ xúc phạm, tục tĩu
        'địt', 'đụ', 'lồn', 'buồi', 'cặc', 'đéo', 'mẹ mày', 'con chó', 'thằng chó',
        'đồ ngu', 'ngu si', 'đần', 'ngu xuẩn', 'óc chó', 'đầu bò',
        // Scam/spam
        'cho vay lãi suất', 'vay nhanh', 'vay không cần thế chấp', 'làm giàu nhanh',
        'kiếm tiền online', 'nhận tiền ngay', 'miễn phí 100%', 'click vào đây',
        'tặng ngay', 'không mất phí', 'cam kết lợi nhuận', 'đảm bảo thu nhập',
        // Từ ngữ nhạy cảm khác
        'ma túy', 'cần sa', 'heroin', 'cocain', 'thuốc lắc',
        'mại dâm', 'gái gọi', 'massage đặc biệt', 'dịch vụ kín',
    ],

    // Threshold cho OpenAI (0.0 - 1.0)
    'openai_threshold' => env('MODERATION_OPENAI_THRESHOLD', 0.5),

    // Hành động khi phát hiện vi phạm
    // 'reject' - Từ chối ngay lập tức
    // 'flag' - Đánh dấu và chờ duyệt
    // 'warn' - Cảnh báo nhưng vẫn cho phép
    'action_on_violation' => env('MODERATION_ACTION', 'reject'),

    // Gửi email thông báo cho admin khi phát hiện vi phạm
    'notify_admin' => env('MODERATION_NOTIFY_ADMIN', false),
];
