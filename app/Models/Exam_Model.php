<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam_Model extends Model
{
    use HasFactory;
    protected $table = 'exam';
    protected $fillable = [
        'exam_title', 'total_mark', 'exam_type', 'exam_date', 'start_time', 'end_time', 
        'institute_for_id', 'board_id', 'medium_id', 'class_id', 'standard_id', 'stream_id', 'subject_id'
    ];
}
