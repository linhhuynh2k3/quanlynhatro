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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('period_start'); // Kỳ thanh toán bắt đầu
            $table->date('period_end'); // Kỳ thanh toán kết thúc
            
            // Tiền trọ
            $table->decimal('rent_amount', 15, 2)->default(0);
            
            // Điện
            $table->integer('electricity_old_reading')->nullable();
            $table->integer('electricity_new_reading')->nullable();
            $table->decimal('electricity_unit_price', 10, 2)->nullable();
            $table->decimal('electricity_amount', 15, 2)->default(0);
            
            // Nước
            $table->integer('water_old_reading')->nullable();
            $table->integer('water_new_reading')->nullable();
            $table->decimal('water_unit_price', 10, 2)->nullable();
            $table->decimal('water_amount', 15, 2)->default(0);
            
            // Dịch vụ
            $table->decimal('wifi_amount', 15, 2)->default(0);
            $table->decimal('trash_amount', 15, 2)->default(0);
            $table->decimal('other_services_amount', 15, 2)->default(0);
            $table->text('other_services_note')->nullable();
            
            // Tổng tiền
            $table->decimal('total_amount', 15, 2);
            
            // Trạng thái
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['contract_id', 'invoice_date']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
