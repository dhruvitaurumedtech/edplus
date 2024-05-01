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
    public function lecture_type_list(){
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
    //add
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

            // if ($request->id) {
            //     $timetable = Timetable::find($request->id);
            //     if (!$timetable) {
            //         return $this->response([],'Record Not Found',404);
            //     }
            // } else {
            //     $timetable = new Timetable();
            // }

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
    
    

    public function list_timetable(Request $request){
        $validator = validator::make($request->all(),[
            'batch_id'=>'required',
        ]);

        if($validator->fails()) 
        return $this->response([],$validator->errors()->first(),false,400);

        try{
           $timtDT = Timetable::join('subject','subject.id','=','time_table.institute_id')
           ->join('users','users.id','=','time_table.teacher_id')
           ->join('lecture_type','lecture_type.id','=','time_table.lecture_type')
           ->join('batches','batches.id','=','time_table.board_id')
           ->join('standard','standard.id','=','batches.standard_id')
           ->where('time_table.batch_id',$request->batch_id)
           ->select('subject.name as subject','users.firstname','users.lastname','lecture_type.name as lecture_type_name','batches.*','standard.name as standard')
           ->get();

           foreach($timtDT as $timtable){
            $dayofdate = date('D', strtotime($timtable->day));

            $data[] = array('id'=>$timtable->id,
            'date'=>$timtable->day,
            'day'=>$dayofdate,
            'start_time'=>$timtable->start_time,
            'end_time'=>$timtable->end_time,
            'subject_id'=>$timtable->subject_id,
            'subject'=>$timtable->subject,
            'lecture_type_id'=>$timtable->lecture_type,
            'lecture_type'=>$timtable->lecture_type_name,
            'standard_id'=>$timtable->standard_id,
            'standard'=>$timtable->standard,
            'batch_id'=>$timtable->batch_id,
            'batch_name'=>$timtable->batch_name,
            'teacher_id'=>$timtable->teacher_id,
            'teacher'=>$timtable->firstname .' '.$timtable->lastname);
           }

           return $this->response($data,'Data Fetch Successfully');
           
        }catch(Exeption $e){
            return $this->response($e,"Something want Wrong!!", false, 400);
        }
    }
}
