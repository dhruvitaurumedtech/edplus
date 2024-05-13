<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users_sub_qualification extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'users_sub_qualification';
    protected $fillable = [
        'user_id','qualification' ];
}
