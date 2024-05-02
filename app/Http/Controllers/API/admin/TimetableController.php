<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\TimeTableBase;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;

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

    public function for_repeat_list(){
        try{
            $timtblretDT = DB::table('timetable_repeat')->get();
            $timetable_repeat = [];
            foreach($timtblretDT as $reotreDT){
                $timetable_repeat[] = array('id'=>$reotreDT->id,'name'=>$reotreDT->name);
            }

            return $this->response($timetable_repeat,'Data Fetch Successfully');
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
            'start_date'=>'required',
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

            $timetablebase = new TimeTableBase();
            $timetablebase->subject_id = $request->subject_id;
            $timetablebase->batch_id = $request->batch_id;
            $timetablebase->teacher_id = $request->teacher_id;
            $timetablebase->lecture_type = $request->lecture_type;
            $timetablebase->start_date = $request->start_date;
            $timetablebase->end_date = $request->end_date;
            $timetablebase->start_time = $request->start_time;
            $timetablebase->end_time = $request->end_time;
            $timetablebase->repeat = $request->repeat;
            $timetablebase->save();
            
            $lastInsertedId = $timetablebase->id;
            if($request->repeat == 1){
                $timetable = new Timetable();
                $timetable->time_table_base_id = $lastInsertedId;
                $timetable->subject_id = $request->subject_id;
                $timetable->batch_id = $request->batch_id;
                $timetable->teacher_id = $request->teacher_id;
                $timetable->lecture_type = $request->lecture_type;
                $timetable->start_date = $request->start_date;
                $timetable->end_date = $request->end_date;
                $timetable->start_time = $request->start_time;
                $timetable->lecture_date = $request->lecture_date;
                $timetable->end_time = $request->end_time;
                $timetable->repeat = $request->repeat;
                $timetable->save();

            }elseif($request->repeat == 2){
                $start_date = $request->start_date;
                $dayName = date('l', strtotime($start_date));
                $end_date = $request->end_date;

                $start_date = $request->start_date;
                $end_date = $request->end_date;

                $current_date = new DateTime($start_date);
                $end_date = new DateTime($end_date);

                while ($current_date <= $end_date) {
                    $dayName = $current_date->format('l'); 
                    //echo "Date: " . $current_date->format('Y-m-d') . ", Day: $dayName <br>";
                    $lecture_date = $current_date->format('Y-m-d');

                    if($request->repeat == 1){
                        $current_date->modify('+1 week'); 
                    }else{
                        $current_date->modify('+1 day'); 
                    }
                    $timetable = new Timetable();
                    $timetable->time_table_base_id = $lastInsertedId;
                    $timetable->subject_id = $request->subject_id;
                    $timetable->batch_id = $request->batch_id;
                    $timetable->teacher_id = $request->teacher_id;
                    $timetable->lecture_type = $request->lecture_type;
                    $timetable->start_date = $request->start_date;
                    $timetable->end_date = $request->end_date;
                    $timetable->lecture_date = $lecture_date;
                    $timetable->start_time = $request->start_time;
                    $timetable->end_time = $request->end_time;
                    $timetable->repeat = $request->repeat;
                    $timetable->save();

                }
               
                

            }


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
