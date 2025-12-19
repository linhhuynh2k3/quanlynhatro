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
        // Tạo bảng nếu chưa tồn tại
        if (!Schema::hasTable('utility_bills')) {
            Schema::create('utility_bills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
                $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
                $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
                
                // Thông tin hóa đơn
                $table->string('bill_number')->unique()->comment('Số hóa đơn');
                $table->date('bill_date')->comment('Ngày lập hóa đơn');
                $table->date('due_date')->comment('Hạn thanh toán');
                $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
                
                // Tiền phòng
                $table->decimal('room_price', 15, 2)->default(0)->comment('Tiền phòng');
                
                // Điện
                $table->integer('electricity_old_reading')->nullable()->comment('Số điện cũ');
                $table->integer('electricity_new_reading')->nullable()->comment('Số điện mới');
                $table->integer('electricity_usage')->nullable()->comment('Số điện tiêu thụ (kWh)');
                $table->decimal('electricity_price_per_unit', 10, 2)->nullable()->comment('Giá điện/đơn vị');
                $table->decimal('electricity_total', 15, 2)->default(0)->comment('Tổng tiền điện');
                
                // Nước
                $table->integer('water_old_reading')->nullable()->comment('Số nước cũ');
                $table->integer('water_new_reading')->nullable()->comment('Số nước mới');
                $table->integer('water_usage')->nullable()->comment('Số nước tiêu thụ (m³)');
                $table->decimal('water_price_per_unit', 10, 2)->nullable()->comment('Giá nước/đơn vị');
                $table->decimal('water_total', 15, 2)->default(0)->comment('Tổng tiền nước');
                
                // Dịch vụ
                $table->decimal('wifi_price', 15, 2)->default(0)->comment('Tiền wifi');
                $table->decimal('garbage_price', 15, 2)->default(0)->comment('Tiền rác');
                
                // Tổng tiền
                $table->decimal('total_amount', 15, 2)->default(0)->comment('Tổng tiền');
                
                // Thanh toán
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                
                $table->timestamps();
            });
            return;
        }
        
        Schema::table('utility_bills', function (Blueprint $table) {
            // Kiểm tra xem cột đã tồn tại chưa trước khi thêm
            if (!Schema::hasColumn('utility_bills', 'contract_id')) {
                $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade')->after('id');
            }
            if (!Schema::hasColumn('utility_bills', 'listing_id')) {
                $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade')->after('contract_id');
            }
            if (!Schema::hasColumn('utility_bills', 'landlord_id')) {
                $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade')->after('listing_id');
            }
            if (!Schema::hasColumn('utility_bills', 'tenant_id')) {
                $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade')->after('landlord_id');
            }
            
            // Thông tin hóa đơn
            if (!Schema::hasColumn('utility_bills', 'bill_number')) {
                $table->string('bill_number')->unique()->comment('Số hóa đơn')->after('tenant_id');
            }
            if (!Schema::hasColumn('utility_bills', 'bill_date')) {
                $table->date('bill_date')->comment('Ngày lập hóa đơn')->after('bill_number');
            }
            if (!Schema::hasColumn('utility_bills', 'due_date')) {
                $table->date('due_date')->comment('Hạn thanh toán')->after('bill_date');
            }
            if (!Schema::hasColumn('utility_bills', 'status')) {
                $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending')->after('due_date');
            }
            
            // Tiền phòng
            if (!Schema::hasColumn('utility_bills', 'room_price')) {
                $table->decimal('room_price', 15, 2)->default(0)->comment('Tiền phòng')->after('status');
            }
            
            // Điện
            if (!Schema::hasColumn('utility_bills', 'electricity_old_reading')) {
                $table->integer('electricity_old_reading')->nullable()->comment('Số điện cũ')->after('room_price');
            }
            if (!Schema::hasColumn('utility_bills', 'electricity_new_reading')) {
                $table->integer('electricity_new_reading')->nullable()->comment('Số điện mới')->after('electricity_old_reading');
            }
            if (!Schema::hasColumn('utility_bills', 'electricity_usage')) {
                $table->integer('electricity_usage')->nullable()->comment('Số điện tiêu thụ (kWh)')->after('electricity_new_reading');
            }
            if (!Schema::hasColumn('utility_bills', 'electricity_price_per_unit')) {
                $table->decimal('electricity_price_per_unit', 10, 2)->nullable()->comment('Giá điện/đơn vị')->after('electricity_usage');
            }
            if (!Schema::hasColumn('utility_bills', 'electricity_total')) {
                $table->decimal('electricity_total', 15, 2)->default(0)->comment('Tổng tiền điện')->after('electricity_price_per_unit');
            }
            
            // Nước
            if (!Schema::hasColumn('utility_bills', 'water_old_reading')) {
                $table->integer('water_old_reading')->nullable()->comment('Số nước cũ')->after('electricity_total');
            }
            if (!Schema::hasColumn('utility_bills', 'water_new_reading')) {
                $table->integer('water_new_reading')->nullable()->comment('Số nước mới')->after('water_old_reading');
            }
            if (!Schema::hasColumn('utility_bills', 'water_usage')) {
                $table->integer('water_usage')->nullable()->comment('Số nước tiêu thụ (m³)')->after('water_new_reading');
            }
            if (!Schema::hasColumn('utility_bills', 'water_price_per_unit')) {
                $table->decimal('water_price_per_unit', 10, 2)->nullable()->comment('Giá nước/đơn vị')->after('water_usage');
            }
            if (!Schema::hasColumn('utility_bills', 'water_total')) {
                $table->decimal('water_total', 15, 2)->default(0)->comment('Tổng tiền nước')->after('water_price_per_unit');
            }
            
            // Dịch vụ
            if (!Schema::hasColumn('utility_bills', 'wifi_price')) {
                $table->decimal('wifi_price', 15, 2)->default(0)->comment('Tiền wifi')->after('water_total');
            }
            if (!Schema::hasColumn('utility_bills', 'garbage_price')) {
                $table->decimal('garbage_price', 15, 2)->default(0)->comment('Tiền rác')->after('wifi_price');
            }
            
            // Tổng tiền
            if (!Schema::hasColumn('utility_bills', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->comment('Tổng tiền')->after('garbage_price');
            }
            
            // Thanh toán
            if (!Schema::hasColumn('utility_bills', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('total_amount');
            }
            if (!Schema::hasColumn('utility_bills', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('utility_bills', 'notes')) {
                $table->text('notes')->nullable()->after('paid_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utility_bills', function (Blueprint $table) {
            $columns = [
                'contract_id', 'listing_id', 'landlord_id', 'tenant_id',
                'bill_number', 'bill_date', 'due_date', 'status',
                'room_price',
                'electricity_old_reading', 'electricity_new_reading', 'electricity_usage',
                'electricity_price_per_unit', 'electricity_total',
                'water_old_reading', 'water_new_reading', 'water_usage',
                'water_price_per_unit', 'water_total',
                'wifi_price', 'garbage_price', 'total_amount',
                'payment_status', 'paid_at', 'notes'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('utility_bills', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
