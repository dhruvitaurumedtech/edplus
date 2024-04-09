<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance_model extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'attendance';
    protected $fillable = [
        'user_id', 'institute_id', 'student_id', 'attendance', 'date'
    ];
}
