<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'role', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentAlbum()
    {
        return $this->belongsTo(Album::class, 'parent_id');
    }

    public function childrenAlbums()
    {
        return $this->hasMany(Album::class, 'parent_id');
    }

    public static function getUserAlbums($userId)
    {
        return self::where('user_id', $userId)->get();
    }
}
