<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student_fees_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'student_fees';
    protected $fillable = [
        'user_id', 'institute_id', 'student_id', 'subject_id', 'total_fees'
    ];
}
