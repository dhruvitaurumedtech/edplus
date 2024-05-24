<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fees_model extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'fees';
    protected $fillable = [
        'user_id','institute_id', 'board_id', 'medium_id', 'standard_id', 'stream_id', 'subject_id', 'amount', 'due_date',
    ];
}
