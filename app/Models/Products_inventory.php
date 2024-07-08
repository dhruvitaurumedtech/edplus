<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products_inventory extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'products_inventory';
    protected $fillable = [
        'product_id','status', 'quantity'
    ];
}
