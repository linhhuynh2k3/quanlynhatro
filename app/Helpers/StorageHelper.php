<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StorageHelper
{
    /**
     * Store file và tự động copy vào public/storage
     * 
     * @param UploadedFile $file
     * @param string $path Thư mục lưu (vd: 'news', 'listings')
     * @param string $disk Disk name (mặc định 'public')
     * @return string Đường dẫn file đã lưu
     */
    public static function storeAndCopy(UploadedFile $file, string $path, string $disk = 'public'): string
    {
        // Lưu vào storage/app/public (đúng chuẩn Laravel)
        $storedPath = $file->store($path, $disk);
        
        // Copy vào public/storage (để máy khác không cần symbolic link)
        self::copyToPublicStorage($storedPath);
        
        return $storedPath;
    }

    /**
     * Copy file từ storage/app/public sang public/storage
     * 
     * @param string $storagePath Đường dẫn trong storage (vd: 'news/image.jpg')
     */
    public static function copyToPublicStorage(string $storagePath): void
    {
        $sourcePath = storage_path('app/public/' . $storagePath);
        $destinationPath = public_path('storage/' . $storagePath);
        
        // Tạo thư mục đích nếu chưa có
        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0755, true);
        }
        
        // Copy file nếu file nguồn tồn tại
        if (file_exists($sourcePath)) {
            @copy($sourcePath, $destinationPath);
            // Đảm bảo permissions đúng
            if (file_exists($destinationPath)) {
                @chmod($destinationPath, 0644);
            }
        }
    }

    /**
     * Xóa file ở cả 2 nơi
     * 
     * @param string $storagePath Đường dẫn trong storage
     * @param string $disk Disk name (mặc định 'public')
     */
    public static function deleteFromBoth(string $storagePath, string $disk = 'public'): void
    {
        // Xóa từ storage
        Storage::disk($disk)->delete($storagePath);
        
        // Xóa từ public/storage
        $publicPath = public_path('storage/' . $storagePath);
        if (file_exists($publicPath)) {
            unlink($publicPath);
        }
    }

    /**
     * Copy nhiều file (dùng cho listings có nhiều ảnh)
     * 
     * @param array $storagePaths Mảng đường dẫn
     */
    public static function copyMultipleToPublicStorage(array $storagePaths): void
    {
        foreach ($storagePaths as $path) {
            self::copyToPublicStorage($path);
        }
    }
}


