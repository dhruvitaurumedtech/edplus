<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Home_work_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'home_work';
    protected $fillable = [
         'id', 'batch_id', 'subject_id', 'date', 'title', 'description','created_by'
    ];
}
