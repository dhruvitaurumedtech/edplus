<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment_type_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'payment_type';
    protected $fillable = [
        'name'
    ];
   
}
