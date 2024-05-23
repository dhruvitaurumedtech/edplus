<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fees_colletion_model extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'fees_colletion';
    protected $fillable = [
        'institute_id', 'student_id', 'invoice_no', 'date', 'total_amount', 'paid_amount', 'due_amount', 'remaining_amount', 'payment_type', 'transaction_id', 'status'
    ];
}
