<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    use ApiTrait;
    public function add_timetable(Request $request){
        
        $validator = validator::make($request->all(),[
            
            'subject_id'=>'required',
            'batch_id'=>'required',
            'teacher_id'=>'required',
            'lecture_type'=>'required',
            'day'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',

        ]);

        if($validator->fails()) 
        return $this->response([],$validator->errors()->first(),false,400);

        try{
            $timetable = new Timetable();
            $timetable->subject_id = $request->subject_id;
            $timetable->batch_id = $request->batch_id;
            $timetable->teacher_id = $request->teacher_id;
            $timetable->lecture_type = $request->lecture_type;
            $timetable->day = $request->day;
            $timetable->start_time = $request->start_time;
            $timetable->end_time = $request->end_time;
            $timetable->save();
            return $this->response($timetable,'data save');
        }catch(Exeption $e){
            return $this->response($e,"Something want Wrong!!", false, 400);
        }
    }

    public function lecture_type_list(Request $request){
        try{
            $LtypeDT = DB::table('lecture_type')->get();
            $lecture_type = [];
            foreach($LtypeDT as $lectureDT){
                $lecture_type[] = array('id'=>$lectureDT->id,'name'=>$lectureDT->name);
            }

            return $this->response($lecture_type,'Data Fetch Successfully');
        }catch(Exeption $e){
            return $this->response($e,"Something want Wrong!!", false, 400);
        }
    }

    
}
