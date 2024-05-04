<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
    protected $table = 'uploads_tables';
    protected $fillable = [
        'url', 'upload_by'
    ];
}
