<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users_sub_experience extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'users_sub_experience';
    protected $fillable = [
        'user_id','institute_name', 'experience'];
}
