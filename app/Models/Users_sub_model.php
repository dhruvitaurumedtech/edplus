<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users_sub_model extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'users_sub';
    protected $fillable = [
        'user_id', 'phone_no', 'dob', 'address', 'pincode', 'area', 'about_us'];
}
