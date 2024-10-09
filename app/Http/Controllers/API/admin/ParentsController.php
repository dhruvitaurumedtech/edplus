<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\announcements_model;
use App\Models\Attendance_model;
use App\Models\Banner_model;
use App\Models\Exam_Model;
use App\Models\Marks_model;
use App\Models\Parents;
use App\Models\Student_detail;
use App\Models\Subject_model;
use App\Models\Timetable;
use App\Models\Timetables;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ParentsController extends Controller
{
    use ApiTrait;


    public function child_list(Request $request)
    {
        try {            
            $banner_list = Banner_model::where('status', 'active')
            ->Where('user_id', '1')
            ->get(['id', 'banner_image', 'url']);
            
            $banner_array = $banner_list->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'banner_image' => asset($banner->banner_image),
                    'url' => $banner->url ?? ''
                ];
            })->toArray();
            $childs = [];
            $chilsdata = Parents::join('users', 'users.id', '=', 'parents.student_id')
                    ->join('students_details', 'students_details.student_id', '=', 'parents.student_id')
                    ->join('institute_detail', 'institute_detail.id', '=', 'students_details.institute_id')
                    ->where('parents.parent_id', Auth::id())
                    ->where('parents.verify', '1')
                    ->distinct()
                    ->select(
                        'users.firstname',
                        'users.lastname',
                        'users.image',
                        'institute_detail.institute_name',
                        'institute_detail.id as institute_id',
                        'parents.student_id',
                        'students_details.subject_id'
                    )
                    ->whereNull('students_details.deleted_at')
                    ->where('students_details.status','1')
                    ->get();
                
                foreach ($chilsdata as $chilDT) {
                $subids = explode(',', $chilDT->subject_id);
                $subjectQY = Subject_model::whereIN('id', $subids)->get();
                $subjDTs = [];
                foreach ($subjectQY as $subDT) {
                    $subjDTs[] = array('id' => $subDT->id, 'name' => $subDT->name);
                }

                $childs[] = array(
                    'child_id' => $chilDT->student_id,
                    'firstname' => $chilDT->firstname,
                    'lastname' => $chilDT->lastname,
                    'image' => (!empty($chilDT->image)) ? asset($chilDT->image) : asset('profile/no-image.png'),
                    'institute_id' => $chilDT->institute_id,
                    'institute_name' => $chilDT->institute_name,
                    'subjects' => $subjDTs
                );
            }

            $data = ['banner'=>$banner_array,'child_list'=>$childs];
            return $this->response($data, "Child List");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }

    
    private  function convertTo12HourFormat($time24)
    {
        $time = Carbon::createFromFormat('H:i:s', $time24);
        return $time->format('g:i:s A');
    }

    public function parents_child_homescreen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required',
            'institute_id'=>'required'
        ]);
        
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{

            $user_id = Auth::id();
            $fees = [];
            
            //banner
            $bannerss = Banner_model::where('status', 'active')
                ->Where('institute_id', $request->institute_id)
                ->paginate(10);
            if ($bannerss->isEmpty()) {
                $banners = Banner_model::where('status', 'active')
                    ->Where('user_id', '1')
                    ->paginate(10);
            } else {
                $banners = $bannerss;
            }
            $banners_data = [];
            foreach ($banners as $value) {
                $imgpath = asset($value->banner_image);
                $banners_data[] = array(
                    'id' => $value->id,
                    'banner_image' => $imgpath,
                    'url' => $value->url ?? ''
                );
            }

            $getstdntdata = Student_detail::join('users', 'users.id', '=', 'students_details.student_id')
            ->join('institute_detail', 'institute_detail.id', '=', 'students_details.institute_id')
            ->select('users.firstname', 'users.lastname','users.image',
             'institute_detail.institute_name','institute_detail.id as institute_id',
             'students_details.*')
            ->where('students_details.student_id', $request->child_id)
            ->where('students_details.institute_id', $request->institute_id)->first();


            //child detail
            $child_detail = [];
            
            
                $subids = explode(',', $getstdntdata->subject_id);
                $subjectQY = Subject_model::whereIN('id', $subids)->get();
                $subjDTs = [];
                foreach ($subjectQY as $subDT) {
                    $subjDTs[] = array('id' => $subDT->id, 'name' => $subDT->name);
                }

                $child_detail[] = array(
                    'child_id' => $getstdntdata->student_id,
                    'firstname' => $getstdntdata->firstname,
                    'lastname' => $getstdntdata->lastname,
                    'image'=> (!empty($getstdntdata->image)) ? asset($getstdntdata->image) : asset('profile/no-image.png'),
                    'institute_id' => intval($getstdntdata->institute_id),
                    'institute_name' => $getstdntdata->institute_name,
                    'subjects' => $subjDTs
                );

            //today's lecture
            $today = date('l');
            $daysidg = DB::table('days')->where('day',$today)->select('id')->first();
            $todays_lecture = [];
            $todayslect = Timetables::join('subject', 'subject.id', '=', 'timetables.subject_id')
            ->join('users', 'users.id', '=', 'timetables.teacher_id')
            ->join('lecture_type', 'lecture_type.id', '=', 'timetables.lecture_type')
            ->join('batches', 'batches.id', '=', 'timetables.batch_id')
            ->where('timetables.batch_id', $getstdntdata->batch_id)
            ->whereRaw("FIND_IN_SET(timetables.subject_id,?)", [$getstdntdata->subject_id])
            ->where('timetables.day', $daysidg->id)
            ->select(
                'subject.name as subject',
                'users.firstname',
                'users.lastname',
                'users.image',
                'lecture_type.name as lecture_type_name',
                'timetables.start_time',
                'timetables.end_time',
                'timetables.day'
            )
            ->orderBy('timetables.start_time', 'asc')
            ->get();
          
            foreach ($todayslect as $todayslecDT) {
                $todays_lecture[] = array(
                    'subject' => $todayslecDT->subject,
                    'teacher' => $todayslecDT->firstname . ' ' . $todayslecDT->lastname,
                    'teacher_image' =>(!empty($todayslecDT->image)) ? asset($todayslecDT->image) : asset('profile/no-image.png'),
                    'day' => $todayslecDT->day,
                    'lecture_type' => $todayslecDT->lecture_type_name,
                    'start_time' => $this->convertTo12HourFormat($todayslecDT->start_time),
                    'end_time' => $this->convertTo12HourFormat($todayslecDT->end_time),
                );
            }
            
            //announcement
            $announcement = [];
            $announcQY = announcements_model::where('institute_id', $getstdntdata->institute_id)
            ->where('standard_id', $getstdntdata->standard_id)
            ->WhereRaw("FIND_IN_SET($getstdntdata->batch_id, batch_id)")
            ->where(function($query) {
                $query->whereRaw("FIND_IN_SET('6', role_type)")
                      ->orWhereRaw("FIND_IN_SET('5', role_type)");
            })
                ->whereNull('deleted_at')
                ->orderByDesc('created_at')
                ->get();
            foreach ($announcQY as $announcDT) {
                $announcement[] = array(
                    'title' => $announcDT->title,
                    'desc' => $announcDT->detail,
                    'time' => $announcDT->created_at
                );
            }

           
            //upcoming exam
            $examlist = [];
                    $subjectIds = explode(',', $getstdntdata->subject_id);
                    $tdasy = date('Y-m-d');
                    $pdate = new \DateTime($tdasy);
                    $pdate->modify('-1 day');
                    $prDayStr = $pdate->format('Y-m-d');
                    $exams = Exam_Model::join('subject', 'subject.id', '=', 'exam.subject_id')
                    ->join('standard', 'standard.id', '=', 'exam.standard_id')
                    ->where('exam.exam_date','>',$prDayStr)
                    ->where('exam.institute_id', $getstdntdata->institute_id)
                    ->where('exam.board_id', $getstdntdata->board_id)
                    ->where('exam.medium_id', $getstdntdata->medium_id)
                    ->when($getstdntdata->batch_id, function ($query, $batch_id) {
                        return $query->where('exam.batch_id', $batch_id);
                    })
                    ->where('exam.standard_id', $getstdntdata->standard_id)
                    ->whereIn('exam.subject_id', $subjectIds)
                    ->orderBy('exam.created_at', 'desc')
                    ->select('exam.*', 'subject.name as subject', 'standard.name as standard')
                    ->get();
                   
                foreach ($exams as $examsDT) {
                    $examlist[] = array(
                        'exam_title' => $examsDT->exam_title,
                        'total_mark' => intval($examsDT->total_mark),
                        'exam_type' => $examsDT->exam_type,
                        'subject' => $examsDT->subject,
                        'standard' => $examsDT->standard,
                        'date' => $examsDT->exam_date,
                        'time' => $this->convertTo12HourFormat($examsDT->start_time) . ' to ' . $this->convertTo12HourFormat($examsDT->end_time),
                    );
                }

            //RESULT 
            $result = [];
            $resultQY = Marks_model::join('exam', 'exam.id', '=', 'marks.exam_id')
            ->join('subject', 'subject.id', '=', 'exam.subject_id')
            ->where('marks.student_id', $request->child_id)
            ->where('exam.institute_id', $getstdntdata->institute_id)
            ->whereIN('exam.subject_id', explode(",",$getstdntdata->subject_id))
            ->select('marks.*', 'subject.name as subject', 'exam.subject_id', 'exam.total_mark', 'exam.exam_type', 'exam.exam_date', 'exam.exam_title')
            ->orderByDesc('marks.created_at')
            ->get();
        foreach ($resultQY as $resultDDt) {
            $highestMarks = Marks_model::where('exam_id', $resultDDt->exam_id)
                    ->max('mark');
            $result[] = array(
                'subject' => $resultDDt->subject,
                'title' => $resultDDt->exam_title . '(' . $resultDDt->exam_type . ')',
                'total_marks' => intval($resultDDt->total_mark),
                'achiveddmarks_marks' => $resultDDt->mark,
                'date' => $resultDDt->exam_date,
                'class_highest' => $highestMarks
            );
        }

        //attendance
        $totalattendlec = [];
        $cumnth = date('Y-m');
        $cmtoday = date('Y-m-d');
        $date = new \DateTime($cmtoday);
        $date->modify('+1 day');
        $nextDayStr = $date->format('Y-m-d');

            $totalattlec = Attendance_model::where('institute_id', $getstdntdata->institute_id)
            ->where('student_id', $getstdntdata->student_id)
            ->where('created_at', 'like', '%' . $cumnth . '%')
            ->where('created_at', '<', $nextDayStr)
            ->whereIN('subject_id', explode(",",$getstdntdata->subject_id))
            ->where('attendance', 'P')->count();
            
            
            $totalmissattlec = Attendance_model::where('institute_id', $getstdntdata->institute_id)
                ->where('student_id', $getstdntdata->student_id)
                ->where('created_at', 'like', '%' . $cumnth . '%')
                ->where('created_at', '<', $nextDayStr)
                ->whereIN('subject_id', explode(",",$getstdntdata->subject_id))
                ->where('attendance', 'A')
                ->count();

            
            $totllect = Timetable::where('lecture_date', 'like', '%' . $cumnth . '%')
                ->where('batch_id', $getstdntdata->batch_id)
                ->where('lecture_date', '<', $nextDayStr)
                ->whereIn('subject_id', explode(',',$getstdntdata->subject_id))
                ->count();    
            
            $totalattendlec = array(
                'total_lectures' => $totalmissattlec + $totalattlec,
                'attend_lectures' => $totalattlec,
                'miss_lectures' => $totalmissattlec //$totalmissattlec 
            );
        $data = [
            'banners_data'=>$banners_data,
            'todays_lecture'=>$todays_lecture,
            'child_detail' => $child_detail,
            'announcement' => $announcement,
            'examlist'=>$examlist,
            'result' => $result,
            'attendance'=>$totalattendlec
        ];

        return $this->response($data, "Data Fetch Successfully");

        }catch(\Exception $e){
            return $this->response($e, "Something went wrong!!.", false, 400);
        }
            
    }
    public function view_profile(Request $request){
        
        $parent_id = Auth::id();
        try{
            $data1 = [];
            $parent = Parents::join('users', 'users.id', '=', 'parents.parent_id')
            ->where('parents.parent_id', $parent_id)
            ->first();
            
            $student = Parents::join('users', 'users.id', '=', 'parents.student_id')
            ->join('students_details', 'students_details.student_id', '=', 'parents.student_id')
            ->where('parents.parent_id', $parent_id)
            ->whereNull('students_details.deleted_at')
            ->where('students_details.status','1')
            ->where('parents.verify', '1')
              ->select('users.*')
              ->get();
              $uniqueStudents = $student->unique('id')->values();

              $response = [];
            foreach($uniqueStudents as $value_student){
                $student2 = Parents::join('users', 'users.id', '=', 'parents.student_id')
                ->join('students_details', 'students_details.student_id', '=', 'parents.student_id')
                ->join('institute_detail', 'institute_detail.id', '=', 'students_details.institute_id')
                ->where('parents.parent_id', $parent_id)
                ->where('parents.student_id', $value_student->id)
                ->select(
                    DB::raw('MAX(users.id) as user_id'),
                    DB::raw('MAX(users.firstname) as firstname'),
                    DB::raw('MAX(users.lastname) as lastname'),
                    DB::raw('MAX(users.email) as email'),
                    DB::raw('MAX(users.mobile) as mobile'),
                    'institute_detail.institute_name',
                    'institute_detail.logo',
                    'institute_detail.address'
                )
                ->whereNull('students_details.deleted_at')
                ->where('students_details.status','1')
                ->groupBy('institute_detail.id', 'institute_detail.institute_name', 'institute_detail.logo', 'institute_detail.address', 'students_details.institute_id')
                ->get();
                $insts=[];
                foreach($student2 as $insdat){
                    $insts[] = ['institute_name'=>$insdat->institute_name,
                    'logo'=>(!empty($insdat->logo)) ? asset($insdat->logo) : asset('profile/no-image.png'),
                    'institute_address'=>$insdat->address,];
                }
                $data2[] = ['child_id'=>$value_student->id,
                            'first_name'=>$value_student->firstname,
                            'last_name'=>$value_student->lastname,
                            'email'=>$value_student->email,
                            'phone'=>$value_student->mobile,
                            'institutes'=>$insts];
            }
            $response =     ['id'=>$parent->id,
                            'first_name'=>$parent->firstname,
                            'last_name'=>$parent->lastname,
                            'email'=>$parent->email,
                            'phone'=>$parent->mobile,
                            'profile'=>(!empty($parent->image))?asset($parent->image):asset('profile/no-image.png'),
                            'address'=>$parent->address,
                            'country_code'=>(!empty($parent->country_code))?$parent->country_code:'+91',
                            'country_code_name'=>$parent->country_code_name,
                            'state'=>$parent->state,
                            'city'=>$parent->city,
                            'pincode'=>$parent->pincode,
                            'child'=>$data2];
                return $this->response($response, "Data Fetch Successfully");
            }catch(\Exception $e){
                return $this->response($e, "Something went wrong!!.", false, 400);
            }

    }

    public function edit_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'country_code' => 'required',
            'country_code_name'=>'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $user = Auth::user();
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->country_code = $request->country_code;
            $user->country_code_name = $request->country_code_name;
            $user->mobile = $request->mobile;
            $user->address = $request->address;
            $user->state = !empty($request->state)?$request->state:'';
            $user->city = !empty($request->city)?$request->city:'';
            $user->pincode = !empty($request->pincode)?$request->pincode:'';
            if ($request->file('image')) {
                $iconFile = $request->file('image');
                $imagePath = $iconFile->store('profile', 'public');
                $user->image = $imagePath;
            }
            
            $user->save();
            
            return $this->response([], "Updated Successfully!");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
}
