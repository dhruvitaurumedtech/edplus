<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;
    protected $table = 'chapters';
    protected $fillable = [
        'subject_id','base_table_id','chapter_name', 'chapter_no','chapter_image', 'created_at', 'updated_at'
    ];
}
