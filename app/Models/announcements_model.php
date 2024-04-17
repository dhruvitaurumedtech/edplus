<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class announcements_model extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'announcements';
    protected $fillable = [
        'user_id', 'institute_id','batch_id', 'board_id', 'medium_id', 'institute_for_id', 'standard_id', 'stream_id', 'subject_id', 'role_type', 'title', 'detail', 'created_at', 'updated_at', 'deleted_at'
    ];
}
