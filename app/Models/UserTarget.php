<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTarget extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'total_time',
        'total_exams',
        'total_practices',
        'total_arenas',
        'min_score',
        'accuracy',
        'day_targets',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
