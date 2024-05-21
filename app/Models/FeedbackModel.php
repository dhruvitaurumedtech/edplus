<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'feedback';
    protected $fillable = [
        'id', 'feedback_to_id', 'institute_id', 'feedback'
    ];
}