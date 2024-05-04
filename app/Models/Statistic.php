<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'total_time',
        'total_exams',
        'total_practices',
        'total_arenas',
        'done_exams',
        'min_score',
        'max_score',
        'avg_score',
        'late_submissions',
        'accuracy',
        'day_stats',
        'most_done_subject',
        'subjects_done_today',
        'total_questions_done',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
