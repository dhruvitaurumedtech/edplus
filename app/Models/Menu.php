<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menu';
    protected $fillable = [
        'menu_name', 'sub_menu_id', 'url'
    ];
    public function permission()
    {
        return $this->hasMany(permission::class, 'menu_id');
    }
}
