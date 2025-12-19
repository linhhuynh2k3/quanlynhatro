<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateTimeHelper
{
    /**
     * Format datetime với timezone Asia/Ho_Chi_Minh
     * 
     * @param mixed $date Carbon instance hoặc string
     * @param string $format Format string (mặc định 'd/m/Y H:i')
     * @return string
     */
    public static function format($date, string $format = 'd/m/Y H:i'): string
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->setTimezone('Asia/Ho_Chi_Minh')->format($format);
    }

    /**
     * Format date only (d/m/Y)
     */
    public static function formatDate($date): string
    {
        return self::format($date, 'd/m/Y');
    }

    /**
     * Format datetime (d/m/Y H:i)
     */
    public static function formatDateTime($date): string
    {
        return self::format($date, 'd/m/Y H:i');
    }

    /**
     * Format time only (H:i)
     */
    public static function formatTime($date): string
    {
        return self::format($date, 'H:i');
    }

    /**
     * Format diffForHumans với timezone đúng
     */
    public static function diffForHumans($date): string
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->setTimezone('Asia/Ho_Chi_Minh')->diffForHumans();
    }
}

