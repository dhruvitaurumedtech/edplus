<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remainder_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'remainder';
    protected $fillable = [
        'type_field','role_type_id', 'student_id', 'date', 'time', 'title', 'message'
    ];
}
