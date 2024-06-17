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
                ->select('users.firstname', 'users.lastname',
                 'institute_detail.institute_name',
                 'institute_detail.id as institute_id',
                 'parents.student_id','students_details.subject_id')->get();
                
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
            ->select('users.firstname', 'users.lastname',
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
                    'institute_id' => $getstdntdata->institute_id,
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
                ->paginate(2);
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
                    ->limit(3)->get();
                   
                foreach ($exams as $examsDT) {
                    $examlist[] = array(
                        'exam_title' => $examsDT->exam_title,
                        'total_mark' => $examsDT->total_mark,
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
            ->orderByDesc('marks.created_at')->limit(3)->get();
        $highestMarks = $resultQY->max('marks');
        foreach ($resultQY as $resultDDt) {
            $result[] = array(
                'subject' => $resultDDt->subject,
                'title' => $resultDDt->exam_title . '(' . $resultDDt->exam_type . ')',
                'total_marks' => $resultDDt->total_marks,
                'achiveddmarks_marks' => boolval($resultDDt->mark),
                'date' => $resultDDt->exam_date,
                'class_highest' => $highestMarks
            );
        }

        //attendance
        $totalattendlec = [];
            $cumnth = date('Y-m');
            $totalattlec = Attendance_model::where('institute_id', $getstdntdata->institute_id)
                ->where('student_id', $request->child_id)
                ->where('created_at', 'like', '%' . $cumnth . '%')
                ->where('attendance', 'P')->count();

            $totllect = Timetable::where('lecture_date', 'like', '%' . $cumnth . '%')
                ->where('batch_id', $getstdntdata->batch_id)
                ->count();
            $totalattendlec = array(
                'total_lectures' => $totllect,
                'attend_lectures' => $totalattlec,
                'miss_lectures' => $totllect - $totalattlec
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
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $parent = Parents::join('users', 'users.id', '=', 'parents.parent_id')
            ->where('parents.parent_id', $request->parent_id)
            ->get();
              $data1 = [];
            foreach($parent as $value_parent){
                $data1[] = ['first_name'=>$value_parent->firstname,
                           'last_name'=>$value_parent->lastname,
                           'email'=>$value_parent->email,
                           'phone'=>$value_parent->mobile,
                           'profile'=>(!empty($value_parent->image))?asset($value_parent->image):asset('no-image.png'),
                           'address'=>$value_parent->address];
            }
            $student = Parents::join('users', 'users.id', '=', 'parents.student_id')
            ->join('students_details', 'students_details.student_id', '=', 'parents.student_id')
            ->join('institute_detail', 'institute_detail.id', '=', 'students_details.institute_id')
              ->where('parents.parent_id', $request->parent_id)
              ->select('users.*','institute_detail.institute_name')
              ->get();
              $data2 = [];
            foreach($student as $value_student){
                $student2 = Parents::join('users', 'users.id', '=', 'parents.student_id')
                ->join('students_details', 'students_details.student_id', '=', 'parents.student_id')
                ->join('institute_detail', 'institute_detail.id', '=', 'students_details.institute_id')
                ->where('parents.parent_id', $request->parent_id)
                ->select('users.*','institute_detail.institute_name')
                ->get();

                $data2[] = ['first_name'=>$value_student->firstname,
                            'last_name'=>$value_student->lastname,
                            'email'=>$value_student->email,
                            'phone'=>$value_student->mobile,
                            'institute_name'=>$value_student->institute_name];
            }
                $response = ['parent'=>$data1,'student'=>$data2];
                // echo "<pre>";print_r($parent);exit;
                return $this->response($response, "Data Fetch Successfully");
            }catch(\Exception $e){
                return $this->response($e, "Something want Wrong!!.", false, 400);
            }

    }
}
