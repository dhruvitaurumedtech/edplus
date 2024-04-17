<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student_detail extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'students_details';
    protected $fillable = [
        'user_id','institute_id','student_id', 'institute_for_id', 'board_id', 
        'medium_id', 'class_id', 'standard_id',
        'stream_id', 'subject_id','batch_id', 'status',
        'created_at', 'updated_at'
    ];
}
