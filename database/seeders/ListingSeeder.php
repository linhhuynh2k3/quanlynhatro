<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $landlords = User::where('role', 'landlord')->get();
        $categories = Category::whereNull('parent_id')->get();

        $listings = [
            [
                'title' => 'Cho thuê phòng trọ đẹp, giá rẻ tại Quận 1',
                'description' => 'Phòng trọ rộng rãi, thoáng mát, có đầy đủ tiện ích. Gần trung tâm, thuận tiện đi lại. Phòng có gác, toilet riêng, wifi miễn phí. Giá cả hợp lý, phù hợp cho sinh viên, công nhân.',
                'address' => '123 Đường Nguyễn Huệ',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 1',
                'ward' => 'Phường Bến Nghé',
                'area' => 25,
                'price' => 3000000,
                'phone' => '0909123456',
                'images' => json_encode(['listings/1.jpg', 'listings/2.jpg', 'listings/3.jpg']),
                'status' => 'approved',
                'is_featured' => true,
                'views' => 150,
            ],
            [
                'title' => 'Phòng trọ khép kín, có điều hòa tại Quận 7',
                'description' => 'Phòng trọ mới xây, khép kín, có điều hòa, nóng lạnh, wifi. Khu vực yên tĩnh, an ninh tốt. Gần chợ, siêu thị, trường học. Phù hợp cho người đi làm.',
                'address' => '456 Đường Nguyễn Thị Thập',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 7',
                'ward' => 'Phường Tân Phong',
                'area' => 30,
                'price' => 4500000,
                'phone' => '0909123457',
                'images' => json_encode(['listings/4.jpg', 'listings/5.jpg']),
                'status' => 'approved',
                'is_featured' => false,
                'views' => 89,
            ],
            [
                'title' => 'Cho thuê nhà trọ 2 tầng tại Quận Bình Thạnh',
                'description' => 'Nhà trọ 2 tầng, mỗi tầng 2 phòng. Phòng rộng, có gác, toilet riêng. Khu vực dân cư đông đúc, tiện ích đầy đủ. Giá thuê ưu đãi cho thuê dài hạn.',
                'address' => '789 Đường Xô Viết Nghệ Tĩnh',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận Bình Thạnh',
                'ward' => 'Phường 25',
                'area' => 40,
                'price' => 5000000,
                'phone' => '0909123458',
                'images' => json_encode(['listings/6.jpg', 'listings/7.jpg', 'listings/8.jpg']),
                'status' => 'approved',
                'is_featured' => true,
                'views' => 120,
            ],
            [
                'title' => 'Phòng trọ giá rẻ cho sinh viên tại Quận 10',
                'description' => 'Phòng trọ giá rẻ, phù hợp cho sinh viên. Gần các trường đại học, có wifi, nước máy. Khu vực an ninh, yên tĩnh. Giá cả hợp lý.',
                'address' => '321 Đường 3/2',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 10',
                'ward' => 'Phường 15',
                'area' => 20,
                'price' => 2500000,
                'phone' => '0909123459',
                'images' => json_encode(['listings/9.jpg', 'listings/10.jpg']),
                'status' => 'approved',
                'is_featured' => false,
                'views' => 67,
            ],
            [
                'title' => 'Căn hộ studio đẹp, hiện đại tại Quận 2',
                'description' => 'Căn hộ studio mới, đầy đủ nội thất, có điều hòa, máy nước nóng, wifi. Khu vực cao cấp, an ninh 24/7. Gần trung tâm thương mại, bệnh viện.',
                'address' => '654 Đường Nguyễn Duy Trinh',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 2',
                'ward' => 'Phường Bình An',
                'area' => 35,
                'price' => 8000000,
                'phone' => '0909123460',
                'images' => json_encode(['listings/11.jpg', 'listings/12.jpg', 'listings/13.jpg']),
                'status' => 'approved',
                'is_featured' => true,
                'views' => 200,
            ],
            [
                'title' => 'Phòng trọ có gác, toilet riêng tại Quận 3',
                'description' => 'Phòng trọ có gác, toilet riêng, có điều hòa. Khu vực trung tâm, thuận tiện đi lại. Gần chợ, siêu thị, bệnh viện. Phù hợp cho người đi làm.',
                'address' => '987 Đường Võ Văn Tần',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 3',
                'ward' => 'Phường 6',
                'area' => 28,
                'price' => 4000000,
                'phone' => '0909123461',
                'images' => json_encode(['listings/14.jpg', 'listings/15.jpg']),
                'status' => 'pending',
                'is_featured' => false,
                'views' => 23,
            ],
            [
                'title' => 'Nhà nguyên căn cho thuê tại Quận 5',
                'description' => 'Nhà nguyên căn 2 tầng, 3 phòng ngủ, 2 toilet. Đầy đủ nội thất, có sân phơi. Khu vực dân cư đông đúc, tiện ích đầy đủ. Phù hợp cho gia đình.',
                'address' => '147 Đường Trần Hưng Đạo',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 5',
                'ward' => 'Phường 11',
                'area' => 80,
                'price' => 12000000,
                'phone' => '0909123462',
                'images' => json_encode(['listings/16.jpg', 'listings/17.jpg', 'listings/18.jpg']),
                'status' => 'approved',
                'is_featured' => false,
                'views' => 95,
            ],
            [
                'title' => 'Phòng trọ giá rẻ, gần trường học tại Quận 8',
                'description' => 'Phòng trọ giá rẻ, gần các trường học, có wifi, nước máy. Khu vực yên tĩnh, an ninh. Phù hợp cho sinh viên, học sinh.',
                'address' => '258 Đường Dương Bá Trạc',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 8',
                'ward' => 'Phường 1',
                'area' => 18,
                'price' => 2000000,
                'phone' => '0909123463',
                'images' => json_encode(['listings/19.jpg', 'listings/20.jpg']),
                'status' => 'approved',
                'is_featured' => false,
                'views' => 45,
            ],
        ];

        foreach ($listings as $index => $listingData) {
            $totalUnits = rand(1, 5);
            $availableUnits = rand(0, $totalUnits);

            $listing = Listing::create([
                'user_id' => $landlords->random()->id,
                'category_id' => $categories->random()->id,
                'title' => $listingData['title'],
                'slug' => Str::slug($listingData['title']) . '-' . ($index + 1),
                'description' => $listingData['description'],
                'address' => $listingData['address'],
                'province' => $listingData['province'],
                'district' => $listingData['district'],
                'ward' => $listingData['ward'],
                'area' => $listingData['area'],
                'price' => $listingData['price'],
                'phone' => $listingData['phone'],
                'images' => $listingData['images'],
                'status' => $listingData['status'],
                'is_featured' => $listingData['is_featured'],
                'views' => $listingData['views'],
                'total_units' => $totalUnits,
                'available_units' => $availableUnits,
                'approved_at' => $listingData['status'] === 'approved' ? now()->subDays(rand(1, 30)) : null,
                'expired_at' => $listingData['status'] === 'approved' ? now()->addDays(rand(30, 90)) : null,
                'created_at' => now()->subDays(rand(1, 60)),
            ]);
        }
    }
}

