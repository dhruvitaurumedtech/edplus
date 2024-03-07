<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Subject_sub extends Model
{
    use HasFactory;
    protected $table = 'subject_sub';
    protected $fillable = [
        'user_id', 'institute_id', 'subject_id', 'created_at', 'updated_at',
    ];
}
