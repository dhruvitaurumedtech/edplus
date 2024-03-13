<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'topic';
    protected $fillable = [
        'user_id','institute_id','base_table_id', 'standard_id', 'subject_id', 'chapter_id',
        'video_category_id', 'topic_no', 'topic_name', 'topic_video'
    ];
}
