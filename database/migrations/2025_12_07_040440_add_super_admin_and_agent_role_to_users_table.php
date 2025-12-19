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
        Schema::table('users', function (Blueprint $table) {
            // Thêm trường is_super_admin để xác định admin cao nhất
            $table->boolean('is_super_admin')->default(false)->after('balance');
        });

        // Cập nhật enum role để thêm 'agent'
        // Xử lý khác nhau cho MySQL và PostgreSQL
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            // MySQL: Sử dụng MODIFY COLUMN
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'agent', 'landlord', 'tenant') NOT NULL DEFAULT 'tenant'");
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Kiểm tra xem có constraint check không
            // Nếu là varchar với check constraint, cập nhật constraint
            // Nếu là enum type, xử lý khác
            $constraint = \Illuminate\Support\Facades\DB::selectOne("
                SELECT conname, pg_get_constraintdef(oid) as definition
                FROM pg_constraint
                WHERE conrelid = 'users'::regclass
                AND conname LIKE '%role%'
            ");
            
            if ($constraint) {
                // Xóa constraint cũ
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS {$constraint->conname}");
            }
            
            // Thêm constraint mới với giá trị 'agent'
            \Illuminate\Support\Facades\DB::statement("
                ALTER TABLE users 
                ADD CONSTRAINT users_role_check 
                CHECK (role IN ('admin', 'agent', 'landlord', 'tenant'))
            ");
        } else {
            // Fallback: Drop và tạo lại cột (cho các DB khác)
            // Lưu dữ liệu trước
            $users = \Illuminate\Support\Facades\DB::table('users')->get(['id', 'role']);
            
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'agent', 'landlord', 'tenant'])->default('tenant')->after('email');
            });
            
            // Khôi phục dữ liệu
            foreach ($users as $user) {
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role' => $user->role]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });

        // Khôi phục enum role về ban đầu
        // PostgreSQL không hỗ trợ xóa giá trị từ enum, nên chỉ xử lý MySQL
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'landlord', 'tenant') NOT NULL DEFAULT 'tenant'");
        }
        // PostgreSQL: Không thể xóa giá trị từ enum, cần manual intervention nếu cần
    }
};
