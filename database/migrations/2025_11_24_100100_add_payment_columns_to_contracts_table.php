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
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->foreignId('payment_id')->nullable()->after('paid_at')->constrained('payments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn(['payment_status', 'paid_at', 'payment_id']);
        });
    }
};

