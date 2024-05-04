<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model',
        'foreign_id',
        'result',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeTodayByUser($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->whereDate('created_at', today());
    }
    public function scopeYesterdayByUser($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->whereDate('created_at', today()->subDay());
    }
}
