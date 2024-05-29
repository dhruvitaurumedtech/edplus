<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount_model extends Model
{
    use HasFactory;
    protected $table = 'discount';
    protected $fillable = [
        'institute_id', 'student_id', 'financial_year', 'discount_amount', 'discount_by'
    ];
}
