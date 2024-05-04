<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'content',
        'attachment',
        'author',
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }
    public function comments()
    {
        return $this->hasMany(TopicComment::class);
    }
}
