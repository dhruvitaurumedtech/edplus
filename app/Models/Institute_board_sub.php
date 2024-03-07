<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Institute_board_sub extends Model
{
    use HasFactory;
    protected $table = 'board_sub';
    protected $fillable = [
        'user_id', 'institute_id', 'board_id', 'created_at', 'updated_at'
    ];
}
