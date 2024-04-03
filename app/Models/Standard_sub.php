<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard_sub extends Model
{
    use HasFactory;
    protected $table = 'standard_sub';
    protected $fillable = [
        'user_id', 'institute_id','institute_for_id','board_id','medium_id','class_id', 'standard_id', 'created_at', 'updated_at'
    ];
}
