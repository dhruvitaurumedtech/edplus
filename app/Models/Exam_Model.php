<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_Model extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'exam';
    protected $fillable = [
        'user_id', 'institute_id', 'exam_title', 'total_mark', 'exam_type', 'exam_date', 'start_time', 'end_time',
        'institute_for_id', 'board_id', 'medium_id', 'class_id', 'standard_id', 'stream_id', 'subject_id'
    ];
}
