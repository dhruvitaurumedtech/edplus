<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stream_sub extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'stream_sub';
    protected $fillable = [
        'user_id', 'institute_id','institute_for_id','board_id','medium_id','class_id', 'standard_id', 'stream_id', 'created_at', 'updated_at'
    ];
}
