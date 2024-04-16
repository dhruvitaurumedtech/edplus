<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoAssignToBatch_Sub extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'video_assign_to_batch_sub';
    protected $fillable = [
        'video_assign_id', 'batch_id', 'subject_id'
    ];
}
