<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Student_detail;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;
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
            ->select('users.*','board.name as board_name','standard.name as standard_name','medium.name as medium_name',
                     'batches.batch_name','class.name as class_name')
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
            ->when(!empty($request->exam_name) ,function ($query) use ($request){
                return $query->where('exam.exam_title', $request->exam_name);
            })
            ->when(!empty($request->exam_date) ,function ($query) use ($request){
                return $query->where('exam.exam_date', $request->exam_date);
            })
            ->when(!empty($request->student_id) ,function ($query) use ($request){
                return $query->where('students_details.student_id', $request->student_id);
            })
            ->get();
            print_r($data);exit;

          } catch (Exception $e) {
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }
}
