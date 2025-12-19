<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $news = [
            [
                'title' => 'Kinh nghiệm tìm phòng trọ giá rẻ tại TP.HCM',
                'excerpt' => 'Bài viết chia sẻ những kinh nghiệm hữu ích khi tìm phòng trọ giá rẻ tại thành phố Hồ Chí Minh.',
                'content' => '<p>Khi tìm phòng trọ tại TP.HCM, bạn cần lưu ý một số điểm quan trọng:</p>
                <ul>
                    <li>Xác định ngân sách phù hợp</li>
                    <li>Chọn khu vực gần nơi làm việc, học tập</li>
                    <li>Kiểm tra tiện ích: wifi, nước, điện</li>
                    <li>Xem xét an ninh khu vực</li>
                    <li>Đọc kỹ hợp đồng trước khi ký</li>
                </ul>
                <p>Chúc bạn tìm được phòng trọ phù hợp!</p>',
                'thumbnail' => 'news/1.jpg',
                'is_published' => true,
            ],
            [
                'title' => 'Những điều cần biết khi thuê phòng trọ',
                'excerpt' => 'Hướng dẫn chi tiết về các vấn đề cần lưu ý khi thuê phòng trọ để tránh rủi ro.',
                'content' => '<p>Khi thuê phòng trọ, bạn cần chú ý:</p>
                <ol>
                    <li><strong>Kiểm tra giấy tờ:</strong> Xác minh quyền sở hữu của chủ nhà</li>
                    <li><strong>Hợp đồng thuê:</strong> Đọc kỹ các điều khoản, đặc biệt là về tiền cọc, tiền thuê</li>
                    <li><strong>Tiện ích:</strong> Kiểm tra wifi, nước, điện, điều hòa</li>
                    <li><strong>An ninh:</strong> Đánh giá mức độ an toàn của khu vực</li>
                    <li><strong>Giao thông:</strong> Xem xét phương tiện đi lại, khoảng cách đến nơi làm việc</li>
                </ol>',
                'thumbnail' => 'news/2.jpg',
                'is_published' => true,
            ],
            [
                'title' => 'Cách trang trí phòng trọ nhỏ gọn, đẹp mắt',
                'excerpt' => 'Bí quyết trang trí phòng trọ nhỏ để tạo không gian sống thoải mái và đẹp mắt.',
                'content' => '<p>Với phòng trọ nhỏ, bạn có thể áp dụng các mẹo sau:</p>
                <ul>
                    <li>Sử dụng màu sáng để tạo cảm giác rộng rãi</li>
                    <li>Tận dụng không gian dọc với kệ treo tường</li>
                    <li>Chọn nội thất đa năng, có thể gấp gọn</li>
                    <li>Sử dụng gương để tạo hiệu ứng không gian</li>
                    <li>Giữ gìn vệ sinh, sắp xếp gọn gàng</li>
                </ul>',
                'thumbnail' => 'news/3.jpg',
                'is_published' => true,
            ],
        ];

        foreach ($news as $item) {
            News::create([
                'user_id' => $admin->id,
                'title' => $item['title'],
                'slug' => Str::slug($item['title']),
                'excerpt' => $item['excerpt'],
                'content' => $item['content'],
                'thumbnail' => $item['thumbnail'],
                'is_published' => $item['is_published'],
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}

