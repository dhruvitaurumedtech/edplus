<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\announcements_model;
use App\Models\Marks_model;
use App\Models\Parents;
use App\Models\Subject_model;
use App\Models\User;
use Illuminate\Http\Request;

class ParentsController extends Controller
{
    public function child_list(Request $request){
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        
        if ($existingUser) {
            $user_id = $request->user_id;
            try{
                //banner
                
                //child
                $childs = [];
                $chilsdata = Parents::join('users','users.id','=','parents.student_id')
                ->join('students_details','students_details.student_id','=','parents.student_id')
                ->join('institute_detail','institute_detail.id','=','students_details.institute_id')
                ->where('parents.parent_id',$user_id)->where('parents.verify','1')
                ->select('users.firstname','users.lastname','institute_detail.institute_name')->get();
                foreach($chilsdata as $chilDT){
                    $subids = explode(',',$chilDT->subject_id);
                    $subjectQY = Subject_model::whereIN('id',$subids);
                    $subjDTs = [];
                    foreach($subjectQY as $subDT){
                        $subjDTs[] = array('id'=>$subDT->id,'name'=>$subDT->name);
                    }

                    $childs[] = array('child_id'=>$chilDT->student_id,
                    'firstname'=>$chilDT->firstname,
                    'lastname'=>$chilDT->lastname,
                    'institute_name'=>$chilDT->institute_name,
                    'subjects'=>$subjDTs);
                }

                return response()->json([
                    'status' => '200',
                    'message' => 'Something went wrong',
                    'data'=>$childs
                ]);

            }catch (\Exception $e){
                return response()->json([
                    'status' => '200',
                    'message' => 'Something went wrong',
                    'data'=>[]
                ]);
            }
        }else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }  
    }

    //pending work in below
    public function parents_child_homescreen(Request $request){
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'child_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        
        if ($existingUser) {
            $user_id = $request->user_id;

            $child_detail = [];
            $announcement = [];
            $result = [];
            $fees = [];
            $todays_lecture = [];

                $todays_lecture[] = array('subject' => 'Chemistry', 'teacher' => 'Dianne Russell', 'time' => '03:30 To 05:00 PM');
                
                $announcQY = announcements_model::where('institute_id', $institute_id)
                ->whereRaw("FIND_IN_SET('5', role_type)")
                ->get();
                foreach ($announcQY as $announcDT) {
                    $announcement[] = array(
                        'title' => $announcDT->title,
                        'desc' => $announcDT->detail,
                        'time' => $announcDT->created_at
                    );
                }

                $resultQY = Marks_model::join('exam', 'exam.id', '=', 'marks.exam_id')
                    ->join('subject', 'subject.id', '=', 'exam.subject_id')
                    ->where('marks.student_id', $user_id)
                    ->where('exam.institute_id', $institute_id)
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

            try{
                return response()->json([
                    'status' => '200',
                    'message' => 'Data fetch successfully!',
                    'data'=>array(
                        'child_detail' => $child_detail,
                        'announcement' => $announcement,
                        'result' => $result,
                        'fees' => $fees,
                    ),
                ]);
            }catch (\Exception $e){
                return response()->json([
                    'status' => '200',
                    'message' => 'Something went wrong',
                    'data'=>[]
                ]);
            }
        }else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }
    }
}
