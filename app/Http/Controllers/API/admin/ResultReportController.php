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
            $data=Student_detail::join('users','users.id','=','students_details.student_id')
            ->join('standard','standard.id','=','students_details.standard_id')
            ->join('board','board.id','=','students_details.board_id')
            ->join('medium','medium.id','=','students_details.medium_id')
            ->join('class','class.id','=','students_details.class_id')
            ->join('batches','batches.id','=','students_details.batch_id')
            ->join('marks','marks.student_id','=','students_details.student_id')
            ->join('exam','exam.id','=','marks.exam_id')
            ->select('users.*','board.name as board_name','standard.name as standard_name','medium.name as medium_name','subject.name as subjectname',
                     'batches.batch_name','class.name as class_name')
            ->where('students_details.institute_id',$request->institute_id)
            ->get();

          } catch (Exception $e) {
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }
}
