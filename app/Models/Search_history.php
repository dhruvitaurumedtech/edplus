<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search_history extends Model
{
    use HasFactory;
    protected $table = 'search_history';
    protected $fillable = [
        'user_id','institute_id','title','created_at','updated_at'
    ];
    
}
