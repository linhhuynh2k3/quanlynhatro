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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('listing_id')->nullable()->constrained('listings')->onDelete('set null');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            // Đảm bảo mỗi cặp tenant-landlord chỉ có 1 conversation
            $table->unique(['tenant_id', 'landlord_id', 'listing_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
