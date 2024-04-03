<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medium_sub extends Model
{
    use HasFactory;
    protected $table = 'medium_sub';
    protected $fillable = [
         'user_id', 'institute_id', 'institute_for_id','board_id','medium_id','created_at', 'updated_at'
    ];
}
