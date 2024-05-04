<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'question',
        'answer_1',
        'answer_2',
        'answer_3',
        'answer_4',
        'answer_correct',
        'answer_detail',
        'subject_id',
        'grade',
        'chapter_id',
        'level',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
