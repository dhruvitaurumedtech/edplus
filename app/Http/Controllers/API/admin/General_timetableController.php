<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Class_room_model;
use App\Models\General_timetable_model;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;

class General_timetableController extends Controller
{
    use ApiTrait;
    function create_general_timetable(Request $request){
       
        $existingTimetable = General_timetable_model::where([
            'day' => $request->day,
        ])->first();
        if ($existingTimetable) {
            return $this->response([], 'Timetable entry already exists for this day.', false, 400);
        } else {

        $timetable = General_timetable_model::create([
                    'class_room_id' => $request->class_room_id,
                    'batch_id' => $request->batch_id,
                    'standard_id' => $request->standard_id,
                    'subject_id' => $request->subject_id,
                    'lecture_type' => $request->lecture_type,
                    'day' => $request->day,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
        ]);
           return $this->response([], 'Created general timetable successfully.');
      }
    }
    function display_general_timetable(){
        $timetables = General_timetable_model::all();
        return $this->response($timetables, 'Display general timetable successfully.');
    }
    function edit_general_timetable(Request $request){

        $existingTimetable = General_timetable_model::where('id', $request->id)->first();
        if ($existingTimetable) {
            $existingTimetable->update([
                'class_room_id' => $request->class_room_id,
                'batch_id' => $request->batch_id,
                'standard_id' => $request->standard_id,
                'subject_id' => $request->subject_id,
                'lecture_type' => $request->lecture_type,
                'day' => $request->day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
               
            ]);
            return $this->response([], 'Updated general timetable successfully.');
        }

    }
    function delete_general_timetable(Request $request){
        $timetable = General_timetable_model::findOrFail($request->id);
        $timetable->delete();
        return $this->response([], 'Timetable entry soft deleted successfully.');
    }
    function filter_general_timetable(Request $request){
        $instiute_id = $request->institute_id;
        $day = $request->day;

        Class_room_model::where('institute_id',$instiute_id);
    }

}
