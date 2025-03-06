<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DateTime;

abstract class DefaultModel extends Model
{
    protected $guarded = [];
    public function getCreatedAtAttribute($date)
    {
        $post = new DateTime($date);
        $thisTime = new DateTime(date('Y-m-d H:i:s'));
        $diff = $post->diff($thisTime);

        if ($diff->d != 0) {
            return date('d-m-Y', strtotime($date));
        } else {
            $time = $diff->h . " giờ trước";
            if ($diff->h == 0) {
                $time = $diff->i . " phút trước";
            }
            return $time;
        }
    }

    // public function getUpdatedAtAttribute($date)
    // {
    //     return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('H:i d-m-Y');
    // }
    public function getUpdatedAtAttribute($date)
    {
        if (!$date) {
            return null; // Trả về null nếu không có ngày
        }

        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('H:i d-m-Y');
        } catch (\Exception $e) {
            return $date; // Trả về nguyên bản nếu có lỗi
        }
    }
}