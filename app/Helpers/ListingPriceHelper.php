<?php

namespace App\Helpers;

class ListingPriceHelper
{
    /**
     * Tính giá đăng tin thường
     * 
     * @param string $paymentType daily|weekly|monthly
     * @param int $durationDays Số ngày đăng
     * @return int Giá tính bằng VNĐ
     */
    public static function calculateRegularPrice(string $paymentType, int $durationDays): int
    {
        // Giá tin thường theo bảng giá
        $dailyPrice = 2000; // 2.000₫/ngày
        $weeklyPrice = 12000; // 12.000₫/tuần (7 ngày)
        $monthlyPrice = 48000; // 48.000₫/tháng (30 ngày, giảm 20% từ 60.000₫)
        
        // Tối thiểu 5 ngày cho tin thường
        $minDays = 5;
        if ($durationDays < $minDays) {
            $durationDays = $minDays;
        }

        switch ($paymentType) {
            case 'daily':
                // Tính theo ngày: 2.000₫/ngày
                return $dailyPrice * $durationDays;
                
            case 'weekly':
                // Tính theo tuần: 12.000₫/tuần (7 ngày)
                $weeks = ceil($durationDays / 7);
                return $weeklyPrice * $weeks;
                
            case 'monthly':
                // Tính theo tháng: 48.000₫/tháng (30 ngày)
                $months = ceil($durationDays / 30);
                return $monthlyPrice * $months;
                
            default:
                // Mặc định tính theo ngày
                return $dailyPrice * $durationDays;
        }
    }

    /**
     * Lấy giá đẩy tin (push post)
     */
    public static function getPushPrice(): int
    {
        return 2000; // 2.000₫
    }

    /**
     * Validate duration và payment type
     */
    public static function validateDuration(string $paymentType, int $durationDays): array
    {
        $errors = [];
        
        switch ($paymentType) {
            case 'daily':
                if ($durationDays < 5) {
                    $errors[] = 'Tin thường tối thiểu 5 ngày.';
                }
                break;
                
            case 'weekly':
                if ($durationDays < 7) {
                    $errors[] = 'Tin theo tuần tối thiểu 7 ngày.';
                }
                break;
                
            case 'monthly':
                if ($durationDays < 30) {
                    $errors[] = 'Tin theo tháng tối thiểu 30 ngày.';
                }
                break;
        }
        
        return $errors;
    }
}

