<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Common_announcement extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'common_announcement';
    protected $fillable = [
        'institute_id', 'teacher_id', 'announcement'
    ];
}
