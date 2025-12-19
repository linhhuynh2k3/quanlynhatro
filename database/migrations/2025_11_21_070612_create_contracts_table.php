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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            // Hợp đồng giữa chủ trọ và người thuê cho một bài đăng
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('monthly_price', 15, 2);
            $table->decimal('deposit_amount', 15, 2)->default(0);

            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
