<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timetable extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'time_table';
    protected $fillable = [
        'user_id','institute_id','board_id','medium_id','institute_for','standard_id','stream_id',
        'subject_id','batch_id','teacher_id','lecture_type','day','start_time','end_time'
    ];
}
