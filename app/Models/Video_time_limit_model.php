<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video_time_limit_model extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'video_time_limit';
    protected $fillable = [
        'time', 'institute_id', 'teacher_id', 'created_at', 'updated_at', 'deleted_at'
    ];
}
