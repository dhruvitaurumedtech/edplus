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
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiTrait;
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
                    'image' => (!empty($chilDT->image)) ? asset($chilDT->image) : asset('no-image.png'),
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


    // public function child_list123(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }
    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();

    //     if ($existingUser) {
    //         $user_id = $request->user_id;
    //         try {
    //             //banner

    //             //child
    //             $childs = [];
    //             $chilsdata = Parents::join('users', 'users.id', '=', 'parents.student_id')
    //                 ->join('students_details', 'students_details.student_id', '=', 'parents.student_id')
    //                 ->join('institute_detail', 'institute_detail.id', '=', 'students_details.institute_id')
    //                 ->where('parents.parent_id', $user_id)->where('parents.verify', '1')
    //                 ->select('users.firstname', 'users.lastname', 'institute_detail.institute_name')->get();
    //             foreach ($chilsdata as $chilDT) {
    //                 $subids = explode(',', $chilDT->subject_id);
    //                 $subjectQY = Subject_model::whereIN('id', $subids);
    //                 $subjDTs = [];
    //                 foreach ($subjectQY as $subDT) {
    //                     $subjDTs[] = array('id' => $subDT->id, 'name' => $subDT->name);
    //                 }

    //                 $childs[] = array(
    //                     'child_id' => $chilDT->student_id,
    //                     'firstname' => $chilDT->firstname,
    //                     'lastname' => $chilDT->lastname,
    //                     'institute_name' => $chilDT->institute_name,
    //                     'subjects' => $subjDTs
    //                 );
    //             }

    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Data',
    //                 'data' => $childs
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Something went wrong',
    //                 'data' => []
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ], 400);
    //     }
    // }


    

    //pending work in below
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
                    'image'=> (!empty($getstdntdata->image)) ? asset($getstdntdata->image) : asset('no-image.png'),
                    'institute_id' => intval($getstdntdata->institute_id),
                    'institute_name' => $getstdntdata->institute_name,
                    'subjects' => $subjDTs
                );

            //today's lecture
            $today = date('Y-m-d');
            $todays_lecture = [];
            $todayslect = Timetable::join('subject', 'subject.id', '=', 'time_table.subject_id')
                ->join('users', 'users.id', '=', 'time_table.teacher_id')
                ->join('lecture_type', 'lecture_type.id', '=', 'time_table.lecture_type')
                ->join('batches', 'batches.id', '=', 'time_table.batch_id')
                ->where('time_table.batch_id', $getstdntdata->batch_id)
                ->whereRaw("FIND_IN_SET(?, time_table.subject_id)", [$getstdntdata->subject_id])
                ->where('time_table.lecture_date', $today)
                ->select(
                    'subject.name as subject',
                    'users.firstname',
                    'users.lastname',
                    'lecture_type.name as lecture_type_name',
                    'time_table.start_time',
                    'time_table.end_time',
                    'time_table.lecture_date'
                )
                ->orderBy('time_table.start_time', 'asc')
                //->paginate(2);
                ->get();
            foreach ($todayslect as $todayslecDT) {
                $todays_lecture[] = array(
                    'subject' => $todayslecDT->subject,
                    'teacher' => $todayslecDT->firstname . ' ' . $todayslecDT->lastname,
                    'lecture_date' => $todayslecDT->lecture_date,
                    'lecture_type' => $todayslecDT->lecture_type_name,
                    'start_time' => $todayslecDT->start_time,
                    'end_time' => $todayslecDT->end_time,
                );
            }

            //announcement
            $announcement = [];
            $announcQY = announcements_model::where('institute_id', $getstdntdata->institute_id)
                ->whereRaw("FIND_IN_SET('6', role_type)")
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
                    $exams = Exam_Model::join('subject', 'subject.id', '=', 'exam.subject_id')
                    ->join('standard', 'standard.id', '=', 'exam.standard_id')
                    ->where('exam.exam_date','>',$tdasy)
                    ->where('exam.institute_id', $getstdntdata->institute_id)
                    ->where('exam.board_id', $getstdntdata->board_id)
                    ->where('exam.medium_id', $getstdntdata->medium_id)
                    ->when($getstdntdata->batch_id, function ($query, $batch_id) {
                        return $query->where('exam.batch_id', $batch_id);
                    })
                    ->where('exam.standard_id', $getstdntdata->standard_id)
                    // ->when($stdetail->stream_id, function ($query, $stream_id) {
                    //     return $query->where('exam.stream_id', $stream_id);
                    // })
                    ->whereIn('exam.subject_id', $subjectIds)
                    ->orderBy('exam.created_at', 'desc')
                    ->select('exam.*', 'subject.name as subject', 'standard.name as standard')
                    //->limit(3) I remove this on Parin's request
                    ->get();
                   
                foreach ($exams as $examsDT) {
                    $examlist[] = array(
                        'exam_title' => $examsDT->exam_title,
                        'total_mark' => intval($examsDT->total_mark),
                        'exam_type' => $examsDT->exam_type,
                        'subject' => $examsDT->subject,
                        'standard' => $examsDT->standard,
                        'date' => $examsDT->exam_date,
                        'time' => $examsDT->start_time . ' to ' . $examsDT->end_time,
                    );
                }

            //RESULT 
            $result = [];
            $resultQY = Marks_model::join('exam', 'exam.id', '=', 'marks.exam_id')
            ->join('subject', 'subject.id', '=', 'exam.subject_id')
            ->where('marks.student_id', $request->child_id)
            ->where('exam.institute_id', $getstdntdata->institute_id)
            ->select('marks.*', 'subject.name as subject', 'exam.subject_id', 'exam.total_mark', 'exam.exam_type', 'exam.exam_date', 'exam.exam_title')
            ->orderByDesc('marks.created_at')
            //->limit(3) I remove this on Parin's request
            ->get();
        //$highestMarks = $resultQY->max('mark');
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
            ->where('student_id', $user_id)
            ->where('created_at', 'like', '%' . $cumnth . '%')
            ->where('created_at', '<', $nextDayStr)
            ->where('attendance', 'P')->count();

            $totalmissattlec = Attendance_model::where('institute_id', $getstdntdata->institute_id)
                ->where('student_id', $user_id)
                ->where('created_at', 'like', '%' . $cumnth . '%')
                ->where('created_at', '<', $nextDayStr)
                ->where('attendance', 'A')
                ->count();

            
            $totllect = Timetable::where('lecture_date', 'like', '%' . $cumnth . '%')
                ->where('batch_id', $getstdntdata->batch_id)
                ->where('lecture_date', '<', $nextDayStr)
                //->whereRaw("FIND_IN_SET(?, subject_id)", [$getstdntdata->subject_id])
                ->whereIn('subject_id', explode(',',$getstdntdata->subject_id))
                ->count();    

            $totalattendlec = array(
                'total_lectures' => $totllect,
                'attend_lectures' => $totalattlec,
                'miss_lectures' => $totalmissattlec 
            );
        $data = [
            'banners_data'=>$banners_data,
            'todays_lecture'=>$todays_lecture,
            'child_detail' => $child_detail,
            'announcement' => $announcement,
            'examlist'=>$examlist,
            'result' => $result,
            'attendance'=>$totalattendlec
            // 'fees' => $fees,
        ];

        return $this->response($data, "Data Fetch Successfully");

        }catch(\Exception $e){
            return $this->response($e, "Something want Wrong!!.", false, 400);
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
                ->groupBy('institute_detail.id', 'institute_detail.institute_name', 'institute_detail.logo', 'institute_detail.address', 'students_details.institute_id')
                ->get();
                $insts=[];
                foreach($student2 as $insdat){
                    $insts[] = ['institute_name'=>$insdat->institute_name,
                    'logo'=>(!empty($insdat->logo)) ? asset($insdat->logo) : asset('no-image.png'),
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
                            'profile'=>(!empty($parent->image))?asset($parent->image):asset('no-image.png'),
                            'address'=>$parent->address,
                            'country_code'=>$parent->country_code,
                            'country_code_name'=>$parent->country_code_name,
                            'state'=>$parent->state,
                            'city'=>$parent->city,
                            'pincode'=>$parent->pincode,
                            'child'=>$data2];
                //$response = ['parent'=>$data1,'student'=>$data2];
                // echo "<pre>";print_r($parent);exit;
                return $this->response($response, "Data Fetch Successfully");
            }catch(\Exception $e){
                return $this->response($e, "Something want Wrong!!.", false, 400);
            }

    }

    public function edit_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'state' => 'required|string',
            'city' => 'required|string',
            'pincode' => 'required|string',
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
            $user->state = $request->state;
            $user->city = $request->city;
            $user->pincode = $request->pincode;
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
