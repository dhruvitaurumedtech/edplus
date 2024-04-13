<?php

namespace App\Http\Controllers;

use App\Models\Video_time_limit_model;
use Illuminate\Http\Request;

class Videotimelimitcontroller extends Controller
{
    public function list(Request $request){
        $vidtimQY = Video_time_limit_model::paginate();
        
        return view('topic/video_time_limit',compact('vidtimQY'));
    }

    public function save(Request $request){
        $request->validate([
            'time' => 'required',
        ]);

        $addt = Video_time_limit_model::first();
        if(!empty($addt)){
            Video_time_limit_model::where('id',$addt->id)->update([
                'time'=>$request->input('time'),
            ]);
        }else{
            Video_time_limit_model::create([
                'time'=>$request->input('time'),
            ]);
        }
        
    
        return redirect()->route('videolimit.list')->with('success', 'Created Successfully');
    
    }
}
