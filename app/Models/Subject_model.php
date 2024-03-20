<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'subject';
    protected $fillable = [
        'base_table_id','name','image', 'status', 'created_by', 'updated_by',
    ];
    
}
