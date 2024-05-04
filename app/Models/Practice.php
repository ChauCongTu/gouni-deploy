<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'questions',
        'slug',
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
