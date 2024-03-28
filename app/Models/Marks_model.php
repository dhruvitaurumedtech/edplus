<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marks_model extends Model
{
    use HasFactory;
    protected $table = 'marks';
    protected $fillable = [
        'student_id','exam_id','mark','created_at','updated_at'
    ];
}
