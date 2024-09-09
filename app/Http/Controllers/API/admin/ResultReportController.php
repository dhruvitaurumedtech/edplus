<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Student_detail;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PDF;
use Illuminate\Support\Facades\Validator;

class ResultReportController extends Controller
{
    use ApiTrait;
    function result_report_pdf(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
          try{
            $data=Student_detail::leftjoin('users','users.id','=','students_details.student_id')
            ->leftjoin('standard','standard.id','=','students_details.standard_id')
            ->leftjoin('board','board.id','=','students_details.board_id')
            ->leftjoin('medium','medium.id','=','students_details.medium_id')
            ->leftjoin('class','class.id','=','students_details.class_id')
            ->leftjoin('batches','batches.id','=','students_details.batch_id')
            ->leftjoin('marks','marks.student_id','=','students_details.student_id')
            ->leftjoin('exam','exam.id','=','marks.exam_id')
            ->leftjoin('subject', 'subject.id', '=', 'exam.subject_id')
            ->select('users.*', 
            'board.name as board_name', 
            'standard.name as standard_name', 
            'medium.name as medium_name',
            'batches.batch_name', 
            'class.name as class_name', 
            'marks.mark', 
            'exam.total_mark', 
            'exam.exam_title as exam_name', 
            'exam.exam_date as exam_date','subject.name as subject_name')
            ->where('students_details.institute_id',$request->institute_id)
            ->when(!empty($request->subject_id) ,function ($query) use ($request){
                return $query->where('exam.subject_id', $request->subject_id);
            })
            ->when(!empty($request->board_id) ,function ($query) use ($request){
                return $query->where('students_details.board_id', $request->board_id);
            })
            ->when(!empty($request->class_id) ,function ($query) use ($request){
                return $query->where('students_details.class_id', $request->class_id);
            })
            ->when(!empty($request->board_id), function ($query) use ($request){
                return $query->where('students_details.board_id', $request->board_id);
            })
            ->when(!empty($request->medium_id) ,function ($query) use ($request){
                return $query->where('students_details.medium_id', $request->medium_id);
            })
            ->when(!empty($request->standard_id) ,function ($query) use ($request){
                return $query->where('students_details.standard_id', $request->standard_id);
            })
            ->when(!empty($request->exam_name) ,function ($query) use ($request){
                return $query->where('exam.exam_title', $request->exam_name);
            })
            ->when(!empty($request->exam_date) ,function ($query) use ($request){
                return $query->where('exam.exam_date', $request->exam_date);
            })
            ->when(!empty($request->student_id) ,function ($query) use ($request){
                return $query->where('students_details.student_id', $request->student_id);
            })
            ->get()->toarray();
            $data = ['result_report'=>$data,'request_data'=>$request]; 
           $pdf = PDF::loadView('pdf.result_report', ['data' => $data])->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);;
            
            $folderPath = public_path('pdfs');
    
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }
            $baseFileName = 'result_report.pdf';
            $pdfPath = $folderPath . '/' . $baseFileName;
    
            $counter = 1;
            while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/result_report' . $counter . '.pdf'; 
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
