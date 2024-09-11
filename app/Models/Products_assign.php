<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products_assign extends Model
{
    use HasFactory;
    protected $table = 'products_assign';
    protected $fillable = [
        'user_id','product_id','status', 'quantity'
    ];
}
