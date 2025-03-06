<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DefaultModel;

class Problem extends DefaultModel
{
    use HasFactory;

    protected $table = 'problems'; // Tên bảng
    // protected $primaryKey = 'id'; // Khóa chính
    // public $timestamps = true; // Tự động cập nhật timestamps

    public const STATUS_UNVERIFIED = 'unverified';
    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';

    protected $fillable = [
        'title',
        'topic_id',
        'question',
        'result',
        'level_id',
        'album_id',
        'first_move',
        'scale',
        'user_id',
        'status',
        'decentralization',
    ];

    // protected $casts = [
    //     'created_at' => 'datetime',
    //     'updated_at' => 'datetime',
    // ];

    public function isVerified()
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
