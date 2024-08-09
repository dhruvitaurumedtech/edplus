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
             $stdetails = Attendance_model::join('subject', 'subject.id','=','attendance.subject_id')
                ->where('attendance.institute_id', $request->institute_id)
                ->where('attendance.student_id', $student_id)
                ->where('attendance.date', $request->date)
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
