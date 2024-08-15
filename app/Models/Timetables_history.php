<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timetables_history extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'timetables_history';
    protected $fillable = [
        'batch_id','subject_id','teacher_id','lecture_type','class_room_id','academic_end_date','start_date','end_date','start_time','end_time','day'
    ];
}
