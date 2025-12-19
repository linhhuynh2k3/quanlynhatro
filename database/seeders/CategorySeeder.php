<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Danh mục cha
        $phongTro = Category::create([
            'name' => 'Phòng trọ',
            'slug' => 'phong-tro',
            'parent_id' => null,
            'position' => 1,
            'is_active' => true,
        ]);

        $nhaNguyenCan = Category::create([
            'name' => 'Nhà nguyên căn',
            'slug' => 'nha-nguyen-can',
            'parent_id' => null,
            'position' => 2,
            'is_active' => true,
        ]);

        $canHo = Category::create([
            'name' => 'Căn hộ',
            'slug' => 'can-ho',
            'parent_id' => null,
            'position' => 3,
            'is_active' => true,
        ]);

        $matBang = Category::create([
            'name' => 'Mặt bằng',
            'slug' => 'mat-bang',
            'parent_id' => null,
            'position' => 4,
            'is_active' => true,
        ]);

        // Danh mục con
        Category::create([
            'name' => 'Phòng trọ giá rẻ',
            'slug' => 'phong-tro-gia-re',
            'parent_id' => $phongTro->id,
            'position' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Phòng trọ có gác',
            'slug' => 'phong-tro-co-gac',
            'parent_id' => $phongTro->id,
            'position' => 2,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Phòng trọ khép kín',
            'slug' => 'phong-tro-khep-kin',
            'parent_id' => $phongTro->id,
            'position' => 3,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Nhà trọ cho thuê',
            'slug' => 'nha-tro-cho-thue',
            'parent_id' => $nhaNguyenCan->id,
            'position' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Căn hộ studio',
            'slug' => 'can-ho-studio',
            'parent_id' => $canHo->id,
            'position' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Căn hộ 1 phòng ngủ',
            'slug' => 'can-ho-1-phong-ngu',
            'parent_id' => $canHo->id,
            'position' => 2,
            'is_active' => true,
        ]);
    }
}

