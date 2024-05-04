<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arena extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'grade',
        'subject_id',
        'author',
        'users',
        'max_users',
        'time',
        'questions',
        'question_count',
        'start_at',
        'type',
        'password',
        'status'
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }
    public function questions()
    {
        $questionIds = explode(',', $this->questions);
        return Question::whereIn('id', $questionIds)->get();
    }
    public function joined(){
        $userIds = explode(',', $this->users);
        return User::select('id', 'name', 'username', 'avatar')->whereIn('id', $userIds)->get();
    }
}
