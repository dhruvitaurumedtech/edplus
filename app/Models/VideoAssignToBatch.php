<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoAssignToBatch extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'video_assignbatch';
    protected $fillable = [
        'batch_id', 'video_id', 'standard_id', 'chapter_id', 'subject_id',
    ];
}
