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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // chủ trọ
            $table->foreignId('category_id')->constrained('categories');

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();

            $table->integer('area')->nullable(); // m2
            $table->decimal('price', 15, 2); // giá / tháng
            $table->enum('price_unit', ['month', 'day'])->default('month');

            $table->text('description')->nullable();
            $table->integer('max_guests')->nullable();

            // Trạng thái bài đăng: pending (chờ duyệt), approved, rejected, expired
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');

            $table->boolean('is_featured')->default(false); // tin nổi bật
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
