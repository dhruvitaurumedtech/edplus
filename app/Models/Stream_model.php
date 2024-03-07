<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stream_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'stream';
    protected $fillable = [
        'standard_id', 'name', 'status', 'created_by', 'updated_by'
    ];
    public function subjects()
    {
        return $this->hasMany(Subject_model::class, 'stream_id');
    }

    public function standard()
    {
        return $this->belongsTo(Standard_model::class, 'standard_id');
    }
}
