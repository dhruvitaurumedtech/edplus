<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institute_for_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'institute_for';
    protected $fillable = [
        'name','icon', 'status', 'created_by', 'updated_by',
    ];
   
}
