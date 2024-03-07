<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Standard_sub extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'standard_sub';
    protected $fillable = [
        'user_id', 'institute_id', 'standard_id', 'created_at', 'updated_at'
    ];
}
