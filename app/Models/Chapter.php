<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'subject_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'chap_id');
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
    public function practices()
    {
        return $this->hasMany(Practice::class);
    }
}
