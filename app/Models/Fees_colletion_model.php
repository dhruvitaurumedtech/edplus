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
        'user_id', 'institute_id', 'student_id', 'student_name', 'invoice_no', 'date', 'payment_amount', 'payment_type', 'bank_name', 'transaction_id', 'status'];
}
