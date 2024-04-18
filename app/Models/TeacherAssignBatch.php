<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherAssignBatch extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'teacher_assign_batch';
    protected $fillable = [
        'teacher_id', 'batch_id',
    ];
}
