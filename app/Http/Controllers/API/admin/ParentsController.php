<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\announcements_model;
use App\Models\Banner_model;
use App\Models\Marks_model;
use App\Models\Parents;
use App\Models\Student_detail;
use App\Models\Subject_model;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiTrait;
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
                ->where('parents.parent_id', Auth::id())->where('parents.verify', '1')
                ->select('users.firstname', 'users.lastname', 'institute_detail.institute_name','institute_detail.id as institute_id','parents.student_id')->get();
            foreach ($chilsdata as $chilDT) {
                $subids = explode(',', $chilDT->subject_id);
                $subjectQY = Subject_model::whereIN('id', $subids);
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
        
        $validator = \validator::make($request->all(), [
            'child_id' => 'required',
            'institute_id'=>'request'
        ]);
        
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $user_id = Auth::id();
            
            $child_detail = [];
            $announcement = [];
            $result = [];
            $fees = [];
            $todays_lecture = [];

            $getstdntdata = Student_detail::where('student_id', $request->child_id)
            ->where('institute_id', $request->institute_id)->first();

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

        //     $announcQY = announcements_model::where('institute_id', $getstdntdata->institute_id)
        //         ->whereRaw("FIND_IN_SET('5', role_type)")
        //         ->get();
        //     foreach ($announcQY as $announcDT) {
        //         $announcement[] = array(
        //             'title' => $announcDT->title,
        //             'desc' => $announcDT->detail,
        //             'time' => $announcDT->created_at
        //         );
        //     }

        //     $resultQY = Marks_model::join('exam', 'exam.id', '=', 'marks.exam_id')
        //     ->join('subject', 'subject.id', '=', 'exam.subject_id')
        //     ->where('marks.student_id', $request->child_id)
        //     ->where('exam.institute_id', $getstdntdata->institute_id)
        //     ->select('marks.*', 'subject.name as subject', 'exam.subject_id', 'exam.total_mark', 'exam.exam_type', 'exam.exam_date', 'exam.exam_title')
        //     ->orderByDesc('marks.created_at')->limit(3)->get();
        // $highestMarks = $resultQY->max('marks');
        // foreach ($resultQY as $resultDDt) {
        //     $result[] = array(
        //         'subject' => $resultDDt->subject,
        //         'title' => $resultDDt->exam_title . '(' . $resultDDt->exam_type . ')',
        //         'total_marks' => $resultDDt->total_marks,
        //         'achiveddmarks_marks' => boolval($resultDDt->mark),
        //         'date' => $resultDDt->exam_date,
        //         'class_highest' => $highestMarks
        //     );
        // }

        $data = [
            'todays_lecture'=>$todays_lecture
            // 'child_detail' => $child_detail,
            // 'announcement' => $announcement,
            // 'result' => $result,
            // 'fees' => $fees,
        ];

        return $this->response($data, "Data Fetch Successfully");

        }catch(\Exception $e){
                return $this->response($e, "Something want Wrong!!.", false, 400);
        }
            
    }
}
