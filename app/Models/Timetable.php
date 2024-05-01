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
        'time_table_base_id','subject_id','batch_id','teacher_id','lecture_type','lecture_date','start_date','end_date','start_time','end_time','repeat'
    ];
}
