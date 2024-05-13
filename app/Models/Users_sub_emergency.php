<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users_sub_emergency extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'users_sub_emergency';
    protected $fillable = [
        'user_id', 'name', 'relation_with', 'mobile_no' 
    ];
}
