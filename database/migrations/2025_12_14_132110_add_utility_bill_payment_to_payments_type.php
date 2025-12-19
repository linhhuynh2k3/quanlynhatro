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
            DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('deposit', 'listing_payment', 'booking_payment', 'withdrawal', 'utility_bill_payment') NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_type_check");
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_type_check CHECK (type IN ('deposit', 'listing_payment', 'booking_payment', 'withdrawal', 'utility_bill_payment'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('deposit', 'listing_payment', 'booking_payment', 'withdrawal') NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_type_check");
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_type_check CHECK (type IN ('deposit', 'listing_payment', 'booking_payment', 'withdrawal'))");
        }
    }
};
