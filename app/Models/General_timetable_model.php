<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class General_timetable_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'class_room';
    protected $fillable = [
         'class_room_id', 'batch_id', 'standard_id', 'subject_id', 'lecture_type', 'day', 'start_time', 'end_time',
    ];
}
