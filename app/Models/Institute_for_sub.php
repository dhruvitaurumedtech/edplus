<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institute_for_sub extends Model
{
    use HasFactory;
    protected $table = 'institute_for_sub';
    protected $fillable = [
        'user_id', 'institute_id', 'institute_for_id', 'created_at', 'updated_at'
    ];
}
