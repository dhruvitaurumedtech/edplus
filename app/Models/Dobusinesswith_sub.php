<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dobusinesswith_sub extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'do_business_with_sub';
    protected $fillable = [
        'user_id', 'institute_id','do_business_with_id', 'created_at', 'updated_at'
    ];
}
