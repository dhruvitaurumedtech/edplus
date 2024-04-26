<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\VideoCategory;
class Dobusinesswith_Model extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'do_business_with';
    protected $fillable = [
        'name', 'category_id', 'status', 'created_by', 'updated_by', 'deleted_at'
    ];

    public function category()
    {
        return $this->belongsTo(VideoCategory::class, 'category_id', 'id');
    }
}
