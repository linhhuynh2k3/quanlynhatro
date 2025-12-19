<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class SetSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đặt admin đầu tiên làm super admin nếu chưa có super admin nào
        $superAdmin = User::where('is_super_admin', true)->first();
        
        if (!$superAdmin) {
            $firstAdmin = User::where('role', 'admin')->orderBy('id', 'asc')->first();
            if ($firstAdmin) {
                $firstAdmin->update(['is_super_admin' => true]);
                $this->command->info("Đã đặt {$firstAdmin->name} (ID: {$firstAdmin->id}) làm Super Admin.");
            } else {
                $this->command->warn("Không tìm thấy admin nào để đặt làm Super Admin.");
            }
        } else {
            $this->command->info("Đã có Super Admin: {$superAdmin->name} (ID: {$superAdmin->id}).");
        }
    }
}
