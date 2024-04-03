<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stream_sub extends Model
{
    use HasFactory;
    protected $table = 'stream_sub';
    protected $fillable = [
        'user_id', 'institute_id','institute_for_id','board_id','medium_id','class_id', 'standard_id', 'stream_id', 'created_at', 'updated_at'
    ];
}
