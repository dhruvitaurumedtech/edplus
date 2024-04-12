<?php

namespace App\Http\Controllers;

use App\Models\Video_time_limit_model;
use Illuminate\Http\Request;

class Videotimelimitcontroller extends Controller
{
    public function list(Request $request){
        $vidtimQY = Video_time_limit_model::get();
        
        return view('topic/video_time_limit',compact('vidtimQY'));
    }

    public function add(Request $request){

    }
}
