<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products_inventory extends Model
{
    use HasFactory;
    protected $table = 'products_inventory';
    protected $fillable = [
        'product_id','status', 'quantity'
    ];
}
