<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeTableBase extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'time_table_base';
    protected $fillable = [
        'subject_id','batch_id','class_room_id','teacher_id','lecture_type','start_date','end_date','start_time','end_time','repeat'
    ];
}
