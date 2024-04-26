<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dobusinesswith_Model;

class Dobusinesswith_sub extends Model
{
    use HasFactory;
    protected $table = 'do_business_with_sub';
    protected $fillable = [
        'user_id', 'institute_id', 'do_business_with_id', 'created_at', 'updated_at'
    ];

    public function business()
    {
        return $this->belongsTo(Dobusinesswith_Model::class, 'do_business_with_id', 'id');
    }
}
