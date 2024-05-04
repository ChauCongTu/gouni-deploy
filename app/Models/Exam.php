<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'time',
        'questions',
        'question_count',
        'join_count',
        'complete_count',
        'subject_id',
        'chapter_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
    public function questions()
    {
        $questionIds = explode(',', $this->questions);
        return Question::whereIn('id', $questionIds)->get();
    }
}
