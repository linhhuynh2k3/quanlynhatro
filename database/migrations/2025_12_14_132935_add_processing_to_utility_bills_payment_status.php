<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE utility_bills MODIFY COLUMN payment_status ENUM('pending', 'processing', 'paid', 'failed') NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE utility_bills DROP CONSTRAINT IF EXISTS utility_bills_payment_status_check");
            DB::statement("ALTER TABLE utility_bills ADD CONSTRAINT utility_bills_payment_status_check CHECK (payment_status IN ('pending', 'processing', 'paid', 'failed'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE utility_bills MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE utility_bills DROP CONSTRAINT IF EXISTS utility_bills_payment_status_check");
            DB::statement("ALTER TABLE utility_bills ADD CONSTRAINT utility_bills_payment_status_check CHECK (payment_status IN ('pending', 'paid', 'failed'))");
        }
    }
};
