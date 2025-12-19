<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        Slider::create([
            'title' => 'Tìm phòng trọ phù hợp với bạn',
            'subtitle' => 'Hơn 10,000+ tin đăng mới mỗi ngày',
            'image' => 'sliders/1.jpg',
            'link' => '/listings',
            'position' => 1,
            'is_active' => true,
        ]);

        Slider::create([
            'title' => 'Cho thuê phòng trọ giá rẻ',
            'subtitle' => 'Từ 2 triệu/tháng, đầy đủ tiện ích',
            'image' => 'sliders/2.jpg',
            'link' => '/listings?price_min=2000000',
            'position' => 2,
            'is_active' => true,
        ]);

        Slider::create([
            'title' => 'Phòng trọ khép kín, hiện đại',
            'subtitle' => 'Có điều hòa, wifi, nóng lạnh',
            'image' => 'sliders/3.jpg',
            'link' => '/listings',
            'position' => 3,
            'is_active' => true,
        ]);
    }
}

