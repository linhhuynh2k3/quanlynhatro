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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('credited_user_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('contract_id')
                ->nullable()
                ->after('listing_id')
                ->constrained('contracts')
                ->nullOnDelete();
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('deposit', 'listing_payment', 'booking_payment', 'withdrawal') NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_type_check");
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_type_check CHECK (type IN ('deposit', 'listing_payment', 'booking_payment', 'withdrawal'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('deposit', 'listing_payment') NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_type_check");
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_type_check CHECK (type IN ('deposit', 'listing_payment'))");
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['credited_user_id']);
            $table->dropColumn('credited_user_id');

            $table->dropForeign(['contract_id']);
            $table->dropColumn('contract_id');
        });
    }
};

