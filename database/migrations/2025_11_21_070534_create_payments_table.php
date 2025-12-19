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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Loại giao dịch: deposit (nạp tiền), listing_payment (thanh toán bài đăng)
            $table->enum('type', ['deposit', 'listing_payment']);

            // Nếu là thanh toán cho bài đăng
            $table->foreignId('listing_id')->nullable()->constrained('listings')->onDelete('cascade');

            $table->decimal('amount', 15, 2);
            $table->string('method')->nullable(); // ví dụ: momo, bank, cash (sau này tích hợp cổng thanh toán)
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');

            $table->string('transaction_code')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
