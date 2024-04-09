<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class board extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'board';
    protected $fillable = [
        'institute_for_id', 'name', 'icon', 'status', 'created_by', 'updated_by'
    ];
    public function boardSub()
    {
        return $this->hasMany(Institute_board_sub::class, 'board_id');
    }
    public function classes()
    {
        return $this->hasMany(Class_model::class, 'board_id');
    }

    public function instituteFor()
    {
        return $this->belongsTo(Institute_for_model::class, 'institute_for_id');
    }
}
