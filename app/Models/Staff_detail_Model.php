<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff_detail_Model extends Model
{
    use HasFactory;
    protected $table = 'staff_detail';
    protected $fillable = [
        'user_id', 'institute_id'
    ];
}
