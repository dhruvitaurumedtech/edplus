<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institute_detail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'institute_detail';
    protected $fillable = [
        'unique_id', 'user_id', 'institute_name', 'address', 'contact_no', 'email', 'about_us', 'logo', 'cover_photo', 'country', 'state', 'city', 'pincode', 'open_time', 'close_time', 'gst_number', 'gst_slab', 'website_link', 'instagram_link', 'facebook_link', 'whatsaap_link', 'youtube_link', 'status', 'start_academic_year', 'end_academic_year'
    ];
}
