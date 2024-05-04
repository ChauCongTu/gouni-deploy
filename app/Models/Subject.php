<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'grade',
    ];
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
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
    public function targets()
    {
        return $this->hasMany(UserTarget::class);
    }
}
