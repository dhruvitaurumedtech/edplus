<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
    protected $fillable = ['module_id', 'feature_name'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
