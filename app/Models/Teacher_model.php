<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher_model extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'teacher_detail';
    protected $fillable = [
        'institute_id', 'teacher_id', 'institute_for_id', 'board_id', 'medium_id', 'class_id', 'standard_id', 'stream_id', 'subject_id',  'status', 'note'
    ];
}
