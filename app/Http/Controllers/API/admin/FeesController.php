<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Batches_model;
use App\Models\Fees_colletion_model;
use App\Models\Fees_model;
use App\Models\Payment_type_model;
use App\Models\Student_detail;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    use ApiTrait;
    public function add_fees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'standard_id' => 'required',
            // 'stream_id' => 'required',
            'subject_id' => 'required',
            'amount' => 'required',
            // 'due_date'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $existingFee = Fees_model::where('board_id', $request->board_id)
        ->where('medium_id', $request->medium_id)
        ->where('standard_id', $request->standard_id)
        ->where('stream_id', $request->stream_id)
        ->where('subject_id', $request->subject_id)
        ->first();

        if ($existingFee) {
            return $this->response([], "Fees for the given criteria already exist.", false, 400);
        }

        $fee = new Fees_model;
        $fee->institute_id = $request->institute_id;
        $fee->board_id = $request->board_id;
        $fee->medium_id = $request->medium_id;
        $fee->standard_id = $request->standard_id;
        $fee->stream_id = $request->stream_id;
        $fee->subject_id = $request->subject_id;
        $fee->amount = $request->amount;
        // $fee->due_date = $request->due_date;
        $fee->save();
        return $this->response([], "Fees inserted successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function view_fees_detail(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $fees = Fees_model::join('board', 'board.id', '=', 'fees.board_id')
        ->join('medium', 'medium.id', '=', 'fees.medium_id')
        ->join('standard', 'standard.id', '=', 'fees.standard_id')
        ->leftjoin('stream', 'stream.id', '=', 'fees.stream_id')
        ->join('subject', 'subject.id', '=', 'fees.subject_id')
        ->select(
            'fees.amount',
            'board.name as board_name',
            'medium.name as medium_name',
            'standard.name as standard_name',
            'stream.name as stream_name',
            'subject.name as subject_name'
        )
        ->where('fees.institute_id', $request->institute_id)
        ->get()->toarray();
        $data=[];
        if(!empty($fees)){

        foreach($fees as $value){
             $data[] = [
                  'board_name'=>$value['board_name'],
                  'medium_name'=>$value['medium_name'],
                  'standard_name'=>$value['standard_name'],
                  'stream_name'=>$value['stream_name'],
                  'subject_name'=>$value['subject_name'],
                  'amount'=>$value['amount'],
             ];
             return $this->response($data, "Data Fetch Successfully");
              }
            }else{
                return $this->response([],"No record found", false, 400);
            } 
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
     
    }
    public function paid_fees_student(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'standard_id' => 'required',
            // 'due_date'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $student_response = Student_detail::join('board', 'board.id', '=', 'students_details.board_id')
                                            ->join('medium', 'medium.id', '=', 'students_details.medium_id')
                                            ->join('standard', 'standard.id', '=', 'students_details.standard_id')
                                            ->leftJoin('stream', 'stream.id', '=', 'students_details.stream_id')
                                            ->join('users', 'users.id', '=', 'students_details.student_id')
                                            ->select(
                                                DB::raw("CONCAT(users.firstname, ' ', users.lastname) as student_name"),
                                                        'users.id as student_id',
                                                        'users.image'
                                            )
                                            ->where('students_details.institute_id', $request->institute_id)
                                            ->where('students_details.batch_id', $request->batch_id)
                                            ->where('students_details.board_id', $request->board_id)
                                            ->where('students_details.medium_id', $request->medium_id)
                                            ->where('students_details.standard_id', $request->standard_id)
                                            ->get()
                                            ->toArray();
        $student = [];
        foreach($student_response as $value){
            $student[]=  ['student_id'=>$value['student_id'],
                          'student_name'=>$value['student_name'],
                          'profile'=>!empty($value['image'])?asset($value['image']):asset('profile/no-image.png')];
        }
        return $this->response($student, "Data Fetch Successfully");
    } catch (Exception $e) {
        return $this->response($e, "Invalid token.", false, 400);
    }
    }
    
    //invoice number mate student_id pass karvu padse
    public function payment_type(Request $request){
        try {
            $payment_mode = Payment_type_model::whereNull('deleted_at')->get();
            $data=[];
            foreach($payment_mode as $value){
               $data[] = ['id'=>$value->id,'name'=>$value->name];
            }
            $fees_colletion=Fees_colletion_model::where('student_id',$request->student_id)->first();
            if (!empty($fees_colletion)) {
                $parts = explode('-', $fees_colletion->invoice_no);
                if (count($parts) === 2) {
                    $number = $parts[1];
                }        
                $invoice = $number + 1 ;
            } else {
                $invoice = 1;
            }

           $invoiceNumber = 'INV' . $request->student_id . '-' . str_pad($invoice, 6, '0', STR_PAD_LEFT);
           $data_final = ['payment_type'=>$data,'invoice_number'=>$invoiceNumber,'date'=>date('Y-m-d')];
           return $this->response($data_final, "Successfully Display PaymentType.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
    public function fees_collection(Request $request){
        
    }
}
