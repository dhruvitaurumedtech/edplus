<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Dobusinesswith_Model;

class VideoCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'video_categories';
    protected $fillable = [
        'name', 'status'
    ];

    public function businesses()
    {
        return $this->hasMany(Dobusinesswith_Model::class, 'category_id', 'id');
    }
}
