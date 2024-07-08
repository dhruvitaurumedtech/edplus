<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products_status extends Model
{
    use HasFactory;
    protected $table = 'products_inventory_status';
    protected $fillable = [
        'name'
    ];
}
