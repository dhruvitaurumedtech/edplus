<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance_model;
use App\Models\Student_detail;
use App\Traits\ApiTrait;
use PDF;
use Illuminate\Support\Facades\File;
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
            ->leftJoin('users','users.id','=','students_details.student_id')
            ->select(
                'attendance.*', 
                'class.name as class_name', 
                'board.name as board_name', 
                'medium.name as medium_name', 
                'standard.name as standard_name', 
                'batches.batch_name', 
                'users.firstname',
                'users.lastname',
                'users.email',
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
            ->when(!empty($request->start_date) && !empty($request->end_date), function($query) use ($request) {
                return $query->whereBetween('attendance.date', [$request->start_date, $request->end_date]);
            })
            ->when(!empty($request->date), function ($query) use ($request) {
                return $query->whereDate('attendance.date', $request->date);
            })
            ->when(!empty($request->attendance_status), function ($query) use ($request) {
                return $query->where('attendance.attendance', $request->attendance_status);
            })
            ->get()->toArray();
            // print_r($attendance);exit;
            $data= ['attendance_data'=>$attendance,'request_data'=>$request];
            $pdf = PDF::loadView('pdf.attendance_report', ['data' => $data]);

            $folderPath = public_path('pdfs');

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $baseFileName = 'attendance_report.pdf';
            $pdfPath = $folderPath . '/' . $baseFileName;

            $counter = 1;
            while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/attendance_report' . $counter . '.pdf'; 
                $counter++;
            }
            
            file_put_contents($pdfPath, $pdf->output());
            $pdfUrl = asset('pdfs/' . basename($pdfPath));
            return $this->response($pdfUrl);
            } catch (Exception $e) {
                return $this->response([], "Something want wrong!.", false, 400);
            }

    }
}
