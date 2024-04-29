<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PdfAssignToBatch extends Model
{
    use HasFactory, SoftDeletes;
    protected $table =  'pdf_assign_batch';
    protected $fillable = [
        'pdf_id', 'batch_id', 'standard_id', 'chapter_id', 'subject_id', 'assign_status'
    ];
}
