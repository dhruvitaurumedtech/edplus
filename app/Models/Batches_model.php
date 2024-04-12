<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batches_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'batches';
    protected $fillable = [
        'user_id', 'institute_id', 'board_id','medium_id','stream_id','standard_id','batch_name','subjects','student_capacity'
    ];
}
