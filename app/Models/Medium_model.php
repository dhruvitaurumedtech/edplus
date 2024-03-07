<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medium_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'medium';
    protected $fillable = [
         'name', 'icon','status', 'created_by', 'updated_by'
    ];
    
}
