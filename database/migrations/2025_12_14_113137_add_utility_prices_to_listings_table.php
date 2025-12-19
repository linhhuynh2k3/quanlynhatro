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
            $table->decimal('electricity_price', 10, 2)->nullable()->after('price')->comment('Giá điện (VNĐ/kWh)');
            $table->decimal('water_price', 10, 2)->nullable()->after('electricity_price')->comment('Giá nước (VNĐ/m³)');
            $table->decimal('wifi_price', 10, 2)->nullable()->after('water_price')->comment('Giá wifi (VNĐ/tháng)');
            $table->decimal('garbage_price', 10, 2)->nullable()->after('wifi_price')->comment('Giá rác (VNĐ/tháng)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['electricity_price', 'water_price', 'wifi_price', 'garbage_price']);
        });
    }
};
