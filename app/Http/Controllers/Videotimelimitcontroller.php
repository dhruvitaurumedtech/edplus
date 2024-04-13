<?php

namespace App\Http\Controllers;

use App\Models\Common_announcement;
use App\Models\Institute_detail;
use App\Models\User;
use App\Models\Video_time_limit_model;
use Illuminate\Http\Request;

class Videotimelimitcontroller extends Controller
{
    public function list(Request $request){
        $institute_list = Institute_detail::get()->toArray();
        $teachers = User::where('role_type', 4)->get();
        $Video_time_limit = Video_time_limit_model::get()->toarray();
        $response=[];
        foreach ($Video_time_limit as $value) {
            $institute_response = Institute_detail::whereIn('id', explode(',', $value['institute_id']))->get()->toarray();
            $teacher_response = User::whereIn('id', explode(',', $value['teacher_id']))->get()->toarray();
            $response[] = [
                'id' => $value['id'],
                'time' => $value['time'],
                'institute_show' => $institute_response,
                'teacher_show' => $teacher_response,
            ];
        }
        
        return view('videotimelimit/video_time_limit',compact('institute_list', 'teachers', 'response'));
    }

    public function save(Request $request){
        
        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'teacher_id' => 'required',
            'time' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validation passed, create a new announcement
        $addt = Video_time_limit_model::create([
            'institute_id' => implode(",", $request->institute_id),
            'teacher_id' => implode(",", $request->teacher_id),
            'time' => $request->time,
        ]);


        return redirect()->route('videolimit.list')->with('success', 'Created Successfully');
    
    }

    public function edit(Request $request)
    {
       $times = Video_time_limit_model::where('id', $request->id)->get()->toarray();
        return response()->json(['times' => $times]);
    }

    public function update(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'teacher_id' => 'required',
            'time' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $vtime = Video_time_limit_model::findOrFail($request->id);

        $vtime->update([
            'institute_id' => implode(",", $request->institute_id),
            'teacher_id' => implode(",", $request->teacher_id),
            'time' => $request->time,
        ]);

        return redirect('video-time-limit')->with('success', 'updated successfully.');
    }

    public function destroy(Request $request)
    {
        $timw_id = $request->input('id');
        $tim_list = Video_time_limit_model::find($timw_id);

        if (!$tim_list) {
            return redirect('video-time-limit')->with('error', 'not found');
        }

        $tim_list->delete();

        return redirect('video-time-limit')->with('success', 'deleted successfully');
    }
}
