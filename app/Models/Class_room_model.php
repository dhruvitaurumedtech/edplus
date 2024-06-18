<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Class_room_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'class_room';
    protected $fillable = [
         'institute_id', 'name', 'capacity'
    ];
}
