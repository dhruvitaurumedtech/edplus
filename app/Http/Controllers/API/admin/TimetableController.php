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
            DB::beginTransaction();
            
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

                $start_date = new DateTime($request->start_date);
                $end_date = new DateTime($request->end_date);

                foreach(explode(',',$request->repeat) as $repeat){
                
                $current_date = clone $start_date;
                while ($current_date <= $end_date) {
                   if($current_date->format('l') === $repeat){

                    $lecture_date = $current_date->format('Y-m-d');

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
                    $timetable->repeat = $repeat;
                    $timetable->save();

                   }

                    $current_date->modify('+1 day'); 
                }
            }
            DB::commit();
            return $this->response([],'data save');
        }catch(Exeption $e){
            DB::rollback();
            return $this->response($e,"Something want Wrong!!", false, 400);
        }
    }
    
    

    public function list_timetable_institute(Request $request){
        $validator = validator::make($request->all(),[
            'batch_id'=>'required',
        ]);

        if($validator->fails()) 
        return $this->response([],$validator->errors()->first(),false,400);

        try{
           $timtDT = Timetable::join('subject','subject.id','=','time_table.subject_id')
           ->join('users','users.id','=','time_table.teacher_id')
           ->join('lecture_type','lecture_type.id','=','time_table.lecture_type')
           ->join('batches','batches.id','=','time_table.batch_id')
           ->join('standard','standard.id','=','batches.standard_id')
           ->where('time_table.batch_id',$request->batch_id)
           ->select('subject.name as subject','users.firstname',
           'users.lastname','lecture_type.name as lecture_type_name',
           'batches.batch_name','batches.standard_id','time_table.*','standard.name as standard')
           ->get();
           $data = [];
           foreach($timtDT as $timtable){
            
            $data[] = array('id'=>$timtable->id,
            'date'=>$timtable->lecture_date,
            'day'=>$timtable->repeat,
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

    //edit time table
    public function edit_timetable(Request $request){
        $validator = validator::make($request->all(),[
            
            'subject_id'=>'required',
            'teacher_id'=>'required',
            'lecture_type'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',

        ]);

        if($validator->fails()) 
        return $this->response([],$validator->errors()->first(),false,400);

        try{
            DB::beginTransaction();
            
                $timetableedit = Timetable::find($request->id);
                if (!$timetableedit) {
                    return $this->response([],'Record Not Found',404);
                }

            
            $timetableedit->subject_id = $request->subject_id;
            //$timetableedit->batch_id = $request->batch_id;
            $timetableedit->teacher_id = $request->teacher_id;
            $timetableedit->lecture_type = $request->lecture_type;
            //$timetableedit->start_date = $request->start_date;
            //$timetableedit->end_date = $request->end_date;
            $timetableedit->start_time = $request->start_time;
            $timetableedit->end_time = $request->end_time;
            //$timetableedit->repeat = $request->repeat;
            $timetableedit->save();
            
               
            DB::commit();
            return $this->response([],'data save');
        }catch(Exeption $e){
            DB::rollback();
            return $this->response($e,"Something want Wrong!!", false, 400);
        }
    }
}
