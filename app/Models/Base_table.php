<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Base_table extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'base_table';
    protected $fillable = [
        'institute_for', 'board', 'medium','institute_for_class','standard','stream','status','created_by','updated_by','updated_by','updated_by'
    ];
    
}
