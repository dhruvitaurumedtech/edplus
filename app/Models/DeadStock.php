<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeadStock extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'dead_stocks';
    protected $fillable = [
        'institute_id','item_name', 'no_of_item'
    ];

}
