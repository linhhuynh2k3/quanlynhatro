<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vai trò người dùng: admin (quản lý), landlord (chủ trọ), tenant (người tìm phòng)
            $table->enum('role', ['admin', 'landlord', 'tenant'])
                ->default('tenant')
                ->after('email');

            // Số dư ví của người dùng để nạp tiền / thanh toán đăng tin
            $table->decimal('balance', 15, 2)
                ->default(0)
                ->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'balance']);
        });
    }
};
