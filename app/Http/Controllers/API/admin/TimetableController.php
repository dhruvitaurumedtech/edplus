<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Batches_model;
use App\Models\Institute_detail;
use App\Models\Timetable;
use App\Models\TimeTableBase;
use App\Models\Timetables;
use App\Traits\ApiTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use Exception;
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
            return $this->response($e,"Something went wrong!!", false, 400);
        }
    }

   

    public function days_list(){
        try{
            $daysDT = DB::table('days')->get();
            $timetable_days = [];
            foreach($daysDT as $dayDT){
                $timetable_days[] = array('id'=>$dayDT->id,'day'=>$dayDT->day);
            }

            return $this->response($timetable_days,'Data Fetch Successfully');
        }catch(Exception $e){
            return $this->response($e,"Something went wrong!!", false, 400);
        }
    }


    private  function convertTo12HourFormat($time24) {
        $time = Carbon::createFromFormat('H:i:s', $time24);
        return $time->format('g:i:s A');
    }

    

    //new timetable list
    public function list_timetables_institute(Request $request) {
        $validator = validator::make($request->all(), [
            'batch_id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
    
        try {
            $timtDT = Timetables::join('subject', 'subject.id', '=', 'timetables.subject_id')
                ->join('users', 'users.id', '=', 'timetables.teacher_id')
                ->join('lecture_type', 'lecture_type.id', '=', 'timetables.lecture_type')
                ->join('days', 'days.id', '=', 'timetables.day')
                ->join('batches', 'batches.id', '=', 'timetables.batch_id')
                ->join('standard', 'standard.id', '=', 'batches.standard_id')
                ->leftjoin('class_room', 'class_room.id', '=', 'timetables.class_room_id')
                ->where('timetables.batch_id', $request->batch_id)
                ->select('subject.name as subject', 'users.firstname','class_room.name as class_room',
                    'users.lastname', 'lecture_type.name as lecture_type_name',
                    'batches.batch_name', 'batches.standard_id','days.day as dayname', 'timetables.*', 'standard.name as standard')
                ->orderBy('timetables.start_time', 'asc')
                ->get();
            $groupedData = [];
    
            foreach ($timtDT as $timtable) {
                $day = $timtable->day;
                if (!isset($groupedData[$day])) {
                    $groupedData[$day] = [
                        'day' => $day,
                        'dayname'=>$timtable->dayname,
                        'sub_data' => []
                    ];
                }
                $groupedData[$day]['sub_data'][] = [
                    'id' => $timtable->id,
                    'day' => $timtable->day,
                    'dayname'=>$timtable->dayname,
                    'start_time' => $this->convertTo12HourFormat( $timtable->start_time),
                    'end_time' => $this->convertTo12HourFormat($timtable->end_time),
                    'subject_id' => $timtable->subject_id,
                    'subject' => $timtable->subject,
                    'lecture_type_id' => $timtable->lecture_type,
                    'lecture_type' => $timtable->lecture_type_name,
                    'standard_id' => $timtable->standard_id,
                    'standard' => $timtable->standard,
                    'batch_id' => $timtable->batch_id,
                    'batch_name' => $timtable->batch_name,
                    'class_room_id'=>$timtable->class_room_id,
                    'class_room'=>$timtable->class_room,
                    'teacher_id' => $timtable->teacher_id,
                    'teacher' => $timtable->firstname . ' ' . $timtable->lastname
                ];
            }
    
            $data = array_values($groupedData);
    
            return $this->response($data, 'Data Fetch Successfully');
    
        } catch (Exception $e) {
            return $this->response([], "Something went wrong!!", false, 400);
        }
    }

    //new add timetable
    public function add_timetables(Request $request){
        $validator = validator::make($request->all(),[
            'batch_id'=>'required|exists:batches,id',
            'subject_id'=>'required|exists:subject,id',
            'teacher_id'=>'required|exists:users,id',
            'lecture_type'=>'required',
            'start_time'=>'required',
            'end_time'=>'required|after:start_time',
            'day'=>'required|exists:days,id',
            'class_room_id'=>'required'
            ]);
    
            if ($validator->fails()) {
                return $this->response([], $validator->errors()->first(), false, 400);
            }
            
        try {
        
            $startTime = Carbon::createFromFormat('H:i:s', $request->start_time);
            $endTime = Carbon::createFromFormat('H:i:s', $request->end_time);
            
            if ($startTime->diffInMinutes($endTime, false) < 30) {
                return $this->response([], "Minimum lecture time should be 30 min.", false, 400);
            }

            $insiddt = Batches_model::where('id',$request->batch_id)->first();
            $academic_end_date = Institute_detail::where('id',$insiddt->institute_id)->first();
            
                        $existing = Timetables::where([
                            ['batch_id', $request->batch_id],
                            ['day', $request->day],
                            ['start_time', '<',$request->end_time ],
                            ['end_time', '>',  $request->start_time],
                        ])
                        ->where('id', '!=', $request->id)
                        ->exists();

                        if ($existing) {
                            return $this->response([], "Lecture already scheduled for this day and time!", false, 400);
                        }

                        $existing = Timetables::where([
                            ['teacher_id', $request->teacher_id],
                            ['day', $request->day],
                            ['start_time', '<',$request->end_time ],
                            ['end_time', '>',  $request->start_time],
                        ])
                        ->where('id', '!=', $request->id)
                        ->exists();
                        if ($existing) {
                            return $this->response([], "This teacher is already occupy for same day and time on another lecture!", false, 400);
                        }

                        $existing = Timetables::where([
                            ['class_room_id', $request->class_room_id],
                            ['day', $request->day],
                            ['start_time', '<',$request->end_time ],
                            ['end_time', '>',  $request->start_time],
                        ])
                        ->where('id', '!=', $request->id)
                        ->exists();

                        if ($existing) {
                            return $this->response([], "This Classroom  is already occupy for same day and time on another lecture!", false, 400);
                        }

                        
                        if($request->id){
                            $timetable = Timetables::find($request->id);
                            $msg = 'Successfully updated Timetable';
                        }else{
                            $timetable = new Timetables();
                            $msg = 'Successfully created Timetable';
                        }

                        $timetable->batch_id = $request->batch_id;
                        $timetable->subject_id = $request->subject_id;
                        $timetable->teacher_id = $request->teacher_id;
                        $timetable->lecture_type = $request->lecture_type;
                        $timetable->class_room_id = $request->class_room_id;
                        $timetable->academic_end_date = $academic_end_date->end_academic_year;
                        $timetable->start_time = $request->start_time;
                        $timetable->end_time = $request->end_time;
                        $timetable->day = $request->day;
                        $timetable->save();

            return $this->response([], $msg);
            } catch (Exception $e) {
                return $this->response($e, "Something went wrong!!", false, 400);
            }
    }
    //add
    public function add_timetable(Request $request){
        
        $validator = validator::make($request->all(),[
            'subject_id'=>'required|exists:subject,id',
            'batch_id'=>'required|exists:batches,id',
            'teacher_id'=>'required|exists:users,id',
            'lecture_type'=>'required',
            'start_time'=>'required',
            'end_time'=>'required|after:start_time',
            'day'=>'required',
            'class_room_id'=>'required'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
        
        $startTime = Carbon::createFromFormat('H:i:s', $request->start_time);
        $endTime = Carbon::createFromFormat('H:i:s', $request->end_time);
        
        if ($startTime->diffInMinutes($endTime, false) < 30) {
            return $this->response([], "Minimum lecture time should be 30 min.", false, 400);
        }
        
        $today = Carbon::now();
        $nextDay = $today->next($request->day);
        $start_date = $nextDay->format('Y-m-d');

        $insiddt = Batches_model::where('id',$request->batch_id)->first();
        $enddt = Institute_detail::where('id',$insiddt->institute_id)->first();
        $timetablebase = new TimeTableBase();
        $timetablebase->subject_id = $request->subject_id;
        $timetablebase->batch_id = $request->batch_id;
        $timetablebase->class_room_id = $request->class_room_id;
        $timetablebase->teacher_id = $request->teacher_id;
        $timetablebase->lecture_type = $request->lecture_type;
        $timetablebase->start_date = $start_date;
        $timetablebase->end_date = $enddt->end_academic_year;
        $timetablebase->start_time = $request->start_time;
        $timetablebase->end_time = $request->end_time;
        $timetablebase->repeat = $request->day;
        $timetablebase->save();

        $lastInsertedId = $timetablebase->id;

        $start_date = new DateTime($start_date);
        $end_date = new DateTime($enddt->end_academic_year);
        $days = explode(',', $request->day);
        
        

        if($request->id){
            $timetadt = Timetable::where('id',$request->id)->first();
            Timetable::where('batch_id',$request->batch_id)
            ->whereBetween('lecture_date', [$start_date, $end_date]) //only current date to end date data should be edit , previous data can't be remove or edit
            ->where('subject_id',$timetadt->subject_id)
            ->where('class_room_id',$timetadt->class_room_id)
            ->where('start_time',$timetadt->start_time)
            ->where('end_time',$timetadt->end_time)
            ->where('teacher_id',$timetadt->teacher_id)
            ->where('lecture_type',$timetadt->lecture_type)->forceDelete();
        }
        

        foreach ($days as $day) {
            
            $day = trim(strtolower($day)); // Normalize the day name

            $current_date = clone $start_date;

            while ($current_date <= $end_date) {
                if (strtolower($current_date->format('l')) == $day) {
                    $lecture_date = $current_date->format('Y-m-d');
                    
                    $existing = Timetable::where([
                        ['batch_id', $request->batch_id],
                        ['lecture_date', $lecture_date],
                        ['start_time', '<',$request->end_time ],
                        ['end_time', '>',  $request->start_time],
                    ])->exists();


                    if ($existing) {
                        return $this->response([], "Lecture already scheduled for this date and time!", false, 400);
                    }

                    $existing = Timetable::where([
                        ['teacher_id', $request->teacher_id],
                        ['lecture_date', $lecture_date],
                        ['start_time', '<',$request->end_time ],
                        ['end_time', '>',  $request->start_time],
                    ])->exists();
                    if ($existing) {
                        return $this->response([], "This teacher is already occupy for same date and time on another lecture!", false, 400);
                    }

                    $existing = Timetable::where([
                        ['class_room_id', $request->class_room_id],
                        ['lecture_date', $lecture_date],
                        ['start_time', '<',$request->end_time ],
                        ['end_time', '>',  $request->start_time],
                    ])->exists();
                    if ($existing) {
                        return $this->response([], "This Classroom  is already occupy for same date and time on another lecture!", false, 400);
                    }

                    $timetable = new Timetable();
                    $timetable->time_table_base_id = $lastInsertedId;
                    $timetable->subject_id = $request->subject_id;
                    $timetable->batch_id = $request->batch_id;
                    $timetable->class_room_id = $request->class_room_id;
                    $timetable->teacher_id = $request->teacher_id;
                    $timetable->lecture_type = $request->lecture_type;
                    $timetable->start_date = $start_date;
                    $timetable->end_date = $enddt->end_academic_year;
                    $timetable->lecture_date = $lecture_date;
                    $timetable->start_time = $request->start_time;
                    $timetable->end_time = $request->end_time;
                    $timetable->repeat = $day;
                    $timetable->save();
                }

                $current_date->modify('+1 day');
            }
        }

        return $this->response([], 'Successfully created Timetable');
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }

public function list_timetable_institute(Request $request) {
    $validator = validator::make($request->all(), [
        'batch_id' => 'required',
    ]);

    if ($validator->fails()) {
        return $this->response([], $validator->errors()->first(), false, 400);
    }

    try {
        $timtDT = Timetable::join('subject', 'subject.id', '=', 'time_table.subject_id')
            ->join('users', 'users.id', '=', 'time_table.teacher_id')
            ->join('lecture_type', 'lecture_type.id', '=', 'time_table.lecture_type')
            ->join('batches', 'batches.id', '=', 'time_table.batch_id')
            ->join('standard', 'standard.id', '=', 'batches.standard_id')
            ->leftjoin('class_room', 'class_room.id', '=', 'time_table.class_room_id')
            ->where('time_table.batch_id', $request->batch_id)
            ->select('subject.name as subject', 'users.firstname','class_room.name as class_room',
                'users.lastname', 'lecture_type.name as lecture_type_name',
                'batches.batch_name', 'batches.standard_id', 'time_table.*', 'standard.name as standard')
            ->orderBy('time_table.start_time', 'asc')
            ->get();

        $groupedData = [];

        foreach ($timtDT as $timtable) {
            $date = $timtable->lecture_date;
            if (!isset($groupedData[$date])) {
                $groupedData[$date] = [
                    'date' => $date,
                    'sub_data' => []
                ];
            }
            $groupedData[$date]['sub_data'][] = [
                'id' => $timtable->id,
                'day' => $timtable->repeat,
                'date' => $timtable->lecture_date,
                'start_time' => $this->convertTo12HourFormat( $timtable->start_time),
                'end_time' => $this->convertTo12HourFormat($timtable->end_time),
                'subject_id' => $timtable->subject_id,
                'subject' => $timtable->subject,
                'lecture_type_id' => $timtable->lecture_type,
                'lecture_type' => $timtable->lecture_type_name,
                'standard_id' => $timtable->standard_id,
                'standard' => $timtable->standard,
                'batch_id' => $timtable->batch_id,
                'batch_name' => $timtable->batch_name,
                'class_room_id'=>$timtable->class_room_id,
                'class_room'=>$timtable->class_room,
                'teacher_id' => $timtable->teacher_id,
                'teacher' => $timtable->firstname . ' ' . $timtable->lastname
            ];
        }

        $data = array_values($groupedData);

        return $this->response($data, 'Data Fetch Successfully');

    } catch (Exception $e) {
        return $this->response([], "Something went wrong!!", false, 400);
    }
}    
    
    public function repeat_timetable(Request $request){
            $validator = validator::make($request->all(),[
                'batch_id'=>'required|exists:batches,id',
                'start_date'=>'required|date_format:Y-m-d|date',
                'end_date'=>'required|date_format:Y-m-d|date',
            ]);
    
            if($validator->fails()) 
            return $this->response([],$validator->errors()->first(),false,400);
    
            try{
                $lastDateString = date('Y-m-d');

                $lastDate = Carbon::createFromFormat('Y-m-d', $lastDateString);
                $lastDate->setCentury(Carbon::now()->year - Carbon::now()->year % 100);
                
                $startOfWeek = $lastDate->startOfWeek();

                $endOfWeek = $startOfWeek->copy()->addDays(6);

                $start_date = $startOfWeek->format('Y-m-d');
                $end_date = $endOfWeek->format('Y-m-d');

                $timetablee = Timetable::where('batch_id',$request->batch_id)
                ->whereBetween('lecture_date', [$start_date, $end_date])->get();
                
                $startDateTime = new DateTime($request->start_date);
                $endDateTime = new DateTime($request->end_date);

                foreach($timetablee as $tmidt){
                    $current_date = clone $startDateTime;
                    while ($current_date <= $endDateTime) {
                       
                        $existing = Timetable::where([
                            ['batch_id', $request->batch_id],
                            ['teacher_id', $request->teacher_id],
                            ['lecture_date', $current_date],
                            ['start_time', $request->start_time],
                            ['end_time', $request->end_time],
                        ])->exists();
            
                        if ($existing) {
                            return $this->response([], "Lecture  already Schedule  for this date and time!", false, 400);
                        }
                        
                        $today = $current_date->format('l');
                        
                        if($tmidt->repeat == $today){
                        $timetable = new Timetable();
                        $timetable->time_table_base_id = $tmidt->time_table_base_id;
                        $timetable->subject_id = $tmidt->subject_id;
                        $timetable->batch_id = $tmidt->batch_id;
                        $timetable->teacher_id = $tmidt->teacher_id;
                        $timetable->lecture_type = $tmidt->lecture_type;
                        $timetable->start_date = $startDateTime;
                        $timetable->end_date = $endDateTime;
                        $timetable->lecture_date = $current_date;
                        $timetable->start_time = $tmidt->start_time;
                        $timetable->end_time = $tmidt->end_time;
                        $timetable->repeat = $tmidt->repeat;
                        $timetable->save();
                    }
                        $current_date->modify('+1 day');
                    }
                }

                
                return $this->response([],'Data Repeat Successfully');
            }catch(Exception $e){
                return $this->response($e,"Something went wrong!!", false, 400);
            }
    }    
}
