<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'balance',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    // Quan hệ: user (chủ trọ) có nhiều listings
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    // Quan hệ: user có nhiều thanh toán
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Quan hệ: user có nhiều bình luận
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Hợp đồng với vai trò chủ trọ
    public function landlordContracts()
    {
        return $this->hasMany(Contract::class, 'landlord_id');
    }

    // Hợp đồng với vai trò người thuê
    public function tenantContracts()
    {
        return $this->hasMany(Contract::class, 'tenant_id');
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'agent']);
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true && $this->role === 'admin';
    }

    public function isLandlord(): bool
    {
        return $this->role === 'landlord';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    /**
     * Kiểm tra quyền chỉnh sửa (chỉ super admin mới có quyền)
     */
    public function canEdit(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Kiểm tra quyền xóa (chỉ super admin mới có quyền)
     */
    public function canDelete(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Gửi thông báo reset password
     * Override method này để sử dụng custom notification
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], false));

        $this->notify(new \App\Notifications\ResetPasswordNotification($url));
    }
}
