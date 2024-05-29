<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['module_name'];

    public function Features()
    {
        return $this->hasMany(Feature::class, 'module_id', 'id')->select('id', 'module_id', 'feature_name');
    }
}
