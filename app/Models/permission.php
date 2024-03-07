<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permission extends Model
{
    use HasFactory;
    protected $table = 'permission';
    protected $fillable = [
        'role_id', 'menu_id', 'add', 'edit', 'view', 'delete'
    ];
    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }
}
