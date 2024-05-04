<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'topic_id',
        'author',
        'content',
        'attachment',
        'likes',
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
