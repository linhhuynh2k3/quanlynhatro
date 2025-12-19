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
        Schema::table('landlord_requests', function (Blueprint $table) {
            $table->string('cccd_number')->nullable()->after('phone');
            $table->string('cccd_front_image')->nullable()->after('cccd_number');
            $table->string('cccd_back_image')->nullable()->after('cccd_front_image');
            $table->string('business_license_image')->nullable()->after('cccd_back_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landlord_requests', function (Blueprint $table) {
            $table->dropColumn(['cccd_number', 'cccd_front_image', 'cccd_back_image', 'business_license_image']);
        });
    }
};
