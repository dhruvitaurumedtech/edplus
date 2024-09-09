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
            ->leftJoin('class', 'class.id', '=', 'students_details.class_id')
            ->leftJoin('board', 'board.id', '=', 'students_details.board_id')
            ->leftJoin('medium', 'medium.id', '=', 'students_details.medium_id')
            ->leftJoin('standard', 'standard.id', '=', 'students_details.standard_id')
            ->leftJoin('batches', 'batches.id', '=', 'students_details.batch_id')
            ->select(
                'attendance.*', 
                'class.name as class_name', 
                'board.name as board_name', 
                'medium.name as medium_name', 
                'standard.name as standard_name', 
                'batches.batch_name', 
            )
            ->where('students_details.status','1')
            ->when(!empty($request->institute_id), function ($query) use ($request) {
                return $query->where('students_details.institute_id', $request->institute_id);
            })
            ->when(!empty($request->student_id), function ($query) use ($request) {
                return $query->where('students_details.student_id', $request->student_id);
            })
            ->when(!empty($request->class_id), function ($query) use ($request) {
                return $query->where('students_details.class_id', $request->class_id);
            })
            ->when(!empty($request->medium_id), function ($query) use ($request) {
                return $query->where('students_details.medium_id', $request->medium_id);
            })
            ->when(!empty($request->board_id), function ($query) use ($request) {
                return $query->where('students_details.board_id', $request->board_id);
            })
            ->when(!empty($request->batch_id), function ($query) use ($request) {
                return $query->where('students_details.batch_id', $request->batch_id);
            })
            ->when(!empty($request->subject_id), function ($query) use ($request) {
                return $query->where('students_details.subject_id', 'LIKE', '%' . $request->subject_id . '%');
            })
            ->get()->toArray();

           print_r($attendance);exit;
                     
        } catch (Exception $e) {
            return $this->response([], "Something want wrong!.", false, 400);
        }

    }
}
