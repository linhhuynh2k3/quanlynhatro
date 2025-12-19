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
        Schema::table('listings', function (Blueprint $table) {
            // Thêm các trường cho thanh toán
            $table->integer('duration_days')->default(30)->after('expired_at'); // Số ngày đăng
            $table->enum('payment_type', ['daily', 'weekly', 'monthly'])->default('daily')->after('duration_days'); // Loại thanh toán
            $table->decimal('listing_price', 15, 2)->nullable()->after('payment_type'); // Giá đã thanh toán
            $table->boolean('is_paid')->default(false)->after('listing_price'); // Đã thanh toán chưa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['duration_days', 'payment_type', 'listing_price', 'is_paid']);
        });
    }
};
