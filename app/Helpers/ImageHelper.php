<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Lấy URL ảnh, ưu tiên public/storage, fallback về Storage::url
     * 
     * @param string|null $path Đường dẫn ảnh trong storage
     * @param string $default Ảnh mặc định nếu không tìm thấy
     * @return string URL ảnh
     */
    public static function url(?string $path, string $default = '/images/default.jpg'): string
    {
        if (!$path) {
            return asset($default);
        }

        // Kiểm tra xem file có tồn tại trong public/storage không
        $publicPath = public_path('storage/' . $path);
        if (file_exists($publicPath)) {
            return asset('storage/' . $path);
        }

        // Fallback về Storage::url (cho symbolic link)
        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        // Nếu không tìm thấy, trả về ảnh mặc định
        return asset($default);
    }
}

