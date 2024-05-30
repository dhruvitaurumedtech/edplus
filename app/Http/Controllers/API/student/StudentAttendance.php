<?php

namespace App\Http\Controllers\API\student;

use App\Http\Controllers\Controller;
use App\Models\Attendance_model;
use App\Models\Student_detail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Auth;

class StudentAttendance extends Controller
{

    use ApiTrait;
    // public function attendance_data(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'institute_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'data' => array('errors' => $errorMessages),
    //         ], 400);
    //     }

    //     try {
    //         $token = $request->header('Authorization');
    //         if (strpos($token, 'Bearer ') === 0) {
    //             $token = substr($token, 7);
    //         }
    //         $institute_id = $request->institute_id;
    //         $user_id = $request->user_id;
    //         $adate = $request->date;
    //         $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //         if ($existingUser) {

    //             $stdetails = Attendance_model::join('subject', 'subject.id', 'attendance', 'attendance.student_id')
    //                 ->where('attendance.institute_id', $institute_id)
    //                 ->where('attendance.student_id', $user_id)
    //                 ->where('attendance.created_at', 'like', '%' . $adate . '%')
    //                 ->whereNull('attendance.deleted_at')
    //                 ->select('attendance.*', 'subject.name')
    //                 ->get();

    //             $attenlist = [];
    //             if (!empty($stdetails)) {
    //                 foreach ($stdetails as $stdetail) {
    //                     $attenlist[] = array(
    //                         'id' => $stdetail->id,
    //                         'subject_id' => $stdetail->subject_id,
    //                         'subject_name' => $stdetail->name,
    //                         'attendance' => $stdetail->attendance,
    //                         'date' => $stdetail->date,
    //                     );
    //                 }
    //             }

    //             return response()->json([
    //                 'success' => 200,
    //                 'message' => 'Attendance',
    //                 'data' => $attenlist
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid token.',
    //             ], 400);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'Something went wrong',
    //             'data' => array('error' => $e->getMessage()),
    //         ], 500);
    //     }
    // }

    public function attendance_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            if($request->child_id){
                $student_id = $request->child_id;
            }else{
                $student_id = Auth::id();
            }

            $stdetails = Attendance_model::join('subject', 'subject.id', 'attendance', 'attendance.student_id')
                ->where('attendance.institute_id', $request->institute_id)
                ->where('attendance.student_id', $student_id)
                ->where('attendance.created_at', strtotime($request->date))
                ->whereNull('attendance.deleted_at')
                ->select('attendance.*', 'subject.name')
                ->get();
            $attenlist = [];
            if (!empty($stdetails)) {
                foreach ($stdetails as $stdetail) {
                    $attenlist[] = array(
                        'id' => $stdetail->id,
                        'subject_id' => $stdetail->subject_id,
                        'subject_name' => $stdetail->name,
                        'attendance' => $stdetail->attendance,
                        'date' => $stdetail->date,
                    );
                }
            }
            return $this->response($attenlist, "Attendance");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
}
