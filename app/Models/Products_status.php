<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products_status extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'products_inventory_status';
    protected $fillable = [
        'name'
    ];
}
