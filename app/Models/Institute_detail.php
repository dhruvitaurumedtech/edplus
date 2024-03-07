<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institute_detail extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'institute_detail';
    protected $fillable = [
        'user_id', 'institute_name', 'address', 'contact_no', 'email', 'status', 'created_by', 'updated_by'
    ];
}
