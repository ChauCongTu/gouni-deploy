<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'chap_id',
        'content',
        'view_count',
        'type',
        'likes',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chap_id');
    }

    public function subject() {
        $chapter = Chapter::where('id', $this->chap_id)->first();
        $subject = Subject::where('id', $chapter->subject_id)->first();
        return $subject;
    }

    public function likeLists (){
        $userList = explode(',', $this->likes);
        $userList = User::select('name', 'username')->whereIn('id', $userList)->get();
        return $userList;
    }
}
