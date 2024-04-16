<?php

namespace App\Http\Controllers\API\student;

use App\Http\Controllers\Controller;
use App\Models\Attendance_model;
use App\Models\Student_detail;
use App\Models\User;
use Illuminate\Http\Request;

class StudentAttendance extends Controller
{
    public function attendance_data(Request $request){
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'data' => array('errors' => $errorMessages),
            ], 400);
        }

        try {
            $token = $request->header('Authorization');
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }
            $institute_id = $request->institute_id;
            $user_id = $request->user_id;
            $adate = $request->date;
            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser) {
                
                    $stdetails = Attendance_model::join('subject','subject.id','attendance','attendance.student_id')
                    ->where('attendance.institute_id', $institute_id)
                    ->where('attendance.student_id', $user_id)
                    ->where('attendance.created_at','like','%'.$adate.'%')
                    ->whereNull('attendance.deleted_at')
                    ->select('attendance.*','subject.name')
                    ->get();

                $attenlist = [];
                if (!empty($stdetails)) {
                    foreach ($stdetails as $stdetail) {
                       $attenlist[]=array('id'=>$stdetail->id,
                       'subject_id'=>$stdetail->subject_id,
                       'subject_name'=>$stdetail->name,
                       'attendance'=>$stdetail->attendance,
                        'date'=>$stdetail->date,);
                    }
                }

                return response()->json([
                    'success' => 200,
                    'message' => 'Attendance',
                    'data' => $attenlist
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'data' => array('error' => $e->getMessage()),
            ], 500);
        }
    
    }
}
