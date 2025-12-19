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
            $table->string('approval_status')->default('pending')->after('status');
            $table->timestamp('reserved_at')->nullable()->after('approval_status');
            $table->string('signature_name')->nullable()->after('payment_id');
            $table->text('signature_data')->nullable()->after('signature_name');
            $table->timestamp('terms_accepted_at')->nullable()->after('signature_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'reserved_at', 'signature_name', 'signature_data', 'terms_accepted_at']);
        });
    }
};

