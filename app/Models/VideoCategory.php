<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'video_categories';
    protected $fillable = [
        'name', 'status'
    ];
}
