<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance_model;
use App\Models\Student_detail;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceReportController extends Controller
{
    use ApiTrait;
    function attendance_report_pdf(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $attendance = Student_detail::join('attendance', function($join) {
                $join->on('attendance.student_id', '=', 'students_details.student_id')
                     ->on('attendance.institute_id', '=', 'students_details.institute_id');
            
            })
            ->join('class', 'class.id', '=', 'students_details.class_id')
            ->join('board', 'board.id', '=', 'students_details.board_id')
            ->join('medium', 'medium.id', '=', 'students_details.medium_id')
            ->join('standard', 'standard.id', '=', 'students_details.standard_id')
            ->join('batches', 'batches.id', '=', 'classes.batch_id')
            ->join('subjects', 'subjects.subject_id', '=', 'attendance.subject_id')
            ->select('attendance.*', 'students_details.*', 'classes.*', 'boards.board_name', 'mediums.medium_name', 'standards.standard_name', 'batches.batch_name', 'subjects.subject_name')
            ->get();

            ->get();
            
                     
        } catch (Exception $e) {
            return $this->response([], "Something want wrong!.", false, 400);
        }

    }
}
