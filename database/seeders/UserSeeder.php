<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@homestay.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'balance' => 0,
            'email_verified_at' => now(),
        ]);

        // Chủ trọ
        $landlords = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'landlord1@homestay.com',
                'password' => Hash::make('password'),
                'role' => 'landlord',
                'balance' => 5000000,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'landlord2@homestay.com',
                'password' => Hash::make('password'),
                'role' => 'landlord',
                'balance' => 3000000,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'landlord3@homestay.com',
                'password' => Hash::make('password'),
                'role' => 'landlord',
                'balance' => 2000000,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($landlords as $landlord) {
            User::create($landlord);
        }

        // Người tìm phòng
        $tenants = [
            [
                'name' => 'Phạm Văn D',
                'email' => 'tenant1@homestay.com',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'balance' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Hoàng Thị E',
                'email' => 'tenant2@homestay.com',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'balance' => 0,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($tenants as $tenant) {
            User::create($tenant);
        }
    }
}

