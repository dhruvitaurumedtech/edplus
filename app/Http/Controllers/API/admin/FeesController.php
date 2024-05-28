<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Fees_colletion_model;
use App\Models\Fees_model;
use App\Models\Institute_detail;
use App\Models\Payment_type_model;
use App\Models\Student_detail;
use App\Models\Subject_sub;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeesController extends Controller
{
    
    use ApiTrait;
    public function add_fees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'board_id' => 'required|exists:board,id',
            'medium_id' => 'required|exists:medium,id',
            'standard_id' => 'required|exists:standard,id',
            'subject_id' => 'required|exists:subject,id',
            'amount' => 'required',
            'stream_id' =>'nullable|integer|exists:stream,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $existingFee = Fees_model::where('user_id',Auth::user()->id)
                                 ->where('institute_id', $request->institute_id)
                                 ->where('board_id', $request->board_id)
                                 ->where('medium_id', $request->medium_id)
                                 ->where('standard_id', $request->standard_id)
                                 ->where('stream_id', $request->stream_id)
                                 ->where('subject_id', $request->subject_id)
                                 ->first();

        if ($existingFee) {
            return $this->response([], "Fees for the given criteria already exist.", false, 400);
        }

        $fee = new Fees_model;
        $fee->user_id = Auth::user()->id;
        $fee->institute_id = $request->institute_id;
        $fee->board_id = $request->board_id;
        $fee->medium_id = $request->medium_id;
        $fee->standard_id = $request->standard_id;
        $fee->stream_id = $request->stream_id;
        $fee->subject_id = $request->subject_id;
        $fee->amount = $request->amount;
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
        ->leftjoin('medium', 'medium.id', '=', 'fees.medium_id')
        ->leftjoin('standard', 'standard.id', '=', 'fees.standard_id')
        ->leftjoin('stream', 'stream.id', '=', 'fees.stream_id')
        ->leftjoin('subject', 'subject.id', '=', 'fees.subject_id')
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
           
              }
            }else{
                return $this->response([],"No record found", false, 400);
            } 
            return $this->response($data, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
     
    }
    public function paid_fees_student(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'board_id' => 'required|exists:board,id',
            'medium_id' => 'required|exists:medium,id',
            'standard_id' => 'required|exists:standard,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $query = Student_detail::join('board', 'board.id', '=', 'students_details.board_id')
                                            ->leftJoin('medium', 'medium.id', '=', 'students_details.medium_id')
                                            ->leftJoin('standard', 'standard.id', '=', 'students_details.standard_id')
                                            ->leftJoin('stream', 'stream.id', '=', 'students_details.stream_id')
                                            ->leftJoin('users', 'users.id', '=', 'students_details.student_id')
                                            ->leftJoin('fees_colletion', function ($join) {
                                                $join->on('fees_colletion.student_id', '=', 'students_details.student_id')
                                                    ->whereRaw('fees_colletion.id = (SELECT MAX(id) FROM fees_colletion WHERE student_id = students_details.student_id)');
                                            })
                                            ->select('users.*','fees_colletion.status')
                                            ->where('students_details.institute_id', $request->institute_id)
                                            ->where('students_details.batch_id', $request->batch_id)
                                            ->where('students_details.board_id', $request->board_id)
                                            ->where('students_details.medium_id', $request->medium_id)
                                            ->where('students_details.standard_id', $request->standard_id);
            if (!empty($request->subject_id)) {
            $subjectIds = explode(',', $request->subject_id);
            $query->whereIn('students_details.subject_id', $subjectIds);
            }
            $student_response=$query->get()->toArray();
            $student = [];
            foreach($student_response as $value){
                if($value['status'] == 'paid'){
                    $student[]=  ['student_id'=>$value['id'],
                    'student_name'=>$value['firstname'].' '.$value['lastname'],
                    'profile'=>!empty($value['image'])?asset($value['image']):asset('profile/no-image.png'),
                    'status'=>$value['status']];
                }
            }
            return $this->response($student, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function pending_fees_student(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'batch_id' =>'required|exists:batches,id',
            'board_id' => 'required|exists:board,id',
            'medium_id' => 'required|exists:medium,id',
            'standard_id' => 'required|exists:standard,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $subjectIds = [];
if (!empty($request->subject_id)) {
    $subjectIds = explode(',', $request->subject_id);

}
        $query = Student_detail::join('board', 'board.id', '=', 'students_details.board_id')
                                            ->leftJoin('medium', 'medium.id', '=', 'students_details.medium_id')
                                            ->leftJoin('standard', 'standard.id', '=', 'students_details.standard_id')
                                            ->leftJoin('stream', 'stream.id', '=', 'students_details.stream_id')
                                            ->leftJoin('users', 'users.id', '=', 'students_details.student_id')
                                            ->leftJoin('fees_colletion', function ($join) {
                                                $join->on('fees_colletion.student_id', '=', 'students_details.student_id')
                                                    ->whereRaw('fees_colletion.id = (SELECT MAX(id) FROM fees_colletion WHERE student_id = students_details.student_id)');
                                            })
                                            ->select('users.*','fees_colletion.status','fees_colletion.student_id')
                                            ->where('students_details.institute_id', $request->institute_id)
                                            ->where('students_details.batch_id', $request->batch_id)
                                            ->where('students_details.board_id', $request->board_id)
                                            ->where('students_details.medium_id', $request->medium_id)
                                            ->where('students_details.standard_id', $request->standard_id)
                                            ->where('students_details.status', '1');
                                            foreach ($subjectIds as $subjectId) {
                                                $query->whereRaw("FIND_IN_SET($subjectId, students_details.subject_id)");
                                            }
            $student_response=$query->get()->toArray();
            $student = [];
            foreach($student_response as $value){
                    if($value['student_id'] != $value['id']){
                          $student[]=  ['student_id'=>$value['id'],
                                        'student_name'=>$value['firstname'].' '.$value['lastname'],
                                        'profile'=>!empty($value['image'])?asset($value['image']):asset('profile/no-image.png'),
                                        'status'=>'pending'];
                            
                    }
                  }
            return $this->response($student, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    
    //invoice number mate student_id pass karvu padse
    public function payment_type(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'board_id' => 'required|integer|exists:board,id',
            'medium_id' => 'required|integer|exists:medium,id',
            'standard_id' => 'required|integer|exists:standard,id',
            'subject_id' => 'required|exists:subject,id',
            'stream_id' =>'nullable',
            'student_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $payment_mode = Payment_type_model::whereNull('deleted_at')->get();
            $data=[];
            foreach($payment_mode as $value){
               $data[] = ['id'=>$value->id,'name'=>$value->name];
            }
           $fees_colletion=Fees_colletion_model::where('student_id',$request->student_id)->latest()->first();
            
            if (!empty($fees_colletion)) {
                $amount=$fees_colletion->total_amount - $fees_colletion->paid_amount; 
                $parts = explode('-', $fees_colletion->invoice_no);
                
                if (count($parts) === 2) {
                    $number = $parts[1];
                }        
                $invoice = $number + 1 ;
            } else {
                $invoice = 1;
            }
            
            $student = User::where('id',$request->student_id)->first();
            $student_name = $student->firstname .' '.$student->lastname;
            $invoiceNumber = 'INV' . $request->student_id . '-' . str_pad($invoice, 6, '0', STR_PAD_LEFT);
            $userId = Auth::user()->id;

            $Fee_amount = Fees_model::where('user_id',$userId)
                                     ->where('institute_id', $request->institute_id)
                                     ->where('board_id', $request->board_id)
                                     ->where('medium_id', $request->medium_id)
                                     ->where('standard_id', $request->standard_id)
                                     ->where('stream_id', $request->stream_id)
                                     ->where('subject_id', $request->subject_id)
                                     ->select('amount')
                                     ->first();
                                    //  echo "<pre>";print_r($Fee_amount);exit;
              
             $fees_colletion=Fees_colletion_model::where('student_id',$request->student_id)->get();
             $paid_amount = 0 ;
             foreach($fees_colletion as $value){
                  $paid_amount +=$value->paid_amount;
                $amount=$value->total_amount - $paid_amount; 
                if($amount==0 || !empty($amount)){
                    $total_amount = $amount;
                }else{
                    $total_amount =$Fee_amount->amount;
                }
             }
             $fees_colletion=Fees_colletion_model::where('student_id',$request->student_id)->count();
             if($fees_colletion < 0){
                $total_amount =$Fee_amount->amount;
             }
            $student_histroy=Fees_colletion_model::where('student_id',$request->student_id)->get();
             
            $histroy = [];
            foreach($student_histroy as $value){
                $histroy[] =[
                    'paid_amount'=>$value->paid_amount,
                    'date'=>$value->created_at,
                    'payment_mode'=>$value->payment_type,
                    'invoice_no'=>$value->invoice_no,
                    'transaction_id'=>$value->transaction_id,

                ]; 
            }
            $data_final = ['payment_type'=>$data,
                           'invoice_number'=>$invoiceNumber,
                           'date'=>date('Y-m-d'),
                           'student_id'=>$request->student_id,
                           'student_name'=>$student_name,
                           'total_amount'=>$total_amount,
                        //    'paid_amount'=>(!empty($fees_colletion->paid_amount))?$fees_colletion->paid_amount:'',
                           'histroy'=>$histroy
                          ];
            return $this->response($data_final, "Successfully Display PaymentType.");
        }catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
    public function fees_collection(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id'=>'required|integer',
            'student_id'=>'required|integer',
            'invoice_no' => 'required',
            'date' => 'required',
            'student_name'=>'required|string',
            'total_amount' => 'required|integer',
            'paid_amount' => 'required|integer',
            'remaining_amount' =>'required|integer',
            'payment_type' => 'required',
            'transaction_id'=>'nullable'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
       
        $fee = new Fees_colletion_model;
        $fee->user_id = Auth::user()->id;
        $fee->institute_id = $request->institute_id;
        $fee->student_id = $request->student_id;
        $fee->student_name = $request->student_name;
        $fee->invoice_no = $request->invoice_no;
        $fee->date = $request->date;
        $fee->total_amount = $request->total_amount;
        $fee->paid_amount = $request->paid_amount;
        $fee->remaining_amount = $request->remaining_amount;
        $fee->payment_type = $request->payment_type;
        $fee->transaction_id = (!empty($request->transaction_id)) ? $request->transaction_id : '';
        // $fee->status = ($request->total_amount==$request->paid_amount)?'paid':'pending';
        $fee->save();
        $paid_amount = 0;
        $fees_detail=Fees_colletion_model::where('student_id',$request->student_id)->get();
        foreach($fees_detail as $value){
            $paid_amount += $value->remaining_amount; 

        }
        
        // Fees_colletion_model::where('id', $fee->id)->update(['paid_amount' => $paid_amount]);
        $paid_status=Fees_colletion_model::where('id',$fee->id)->latest()->first();
       
        Fees_colletion_model::where('id', $fee->id)->update(['status'=>($paid_status->remaining_amount=='0')?'paid':'pending']);

        return $this->response([], "Fees Paid successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }

        
    }
    public function display_subject_fees(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id'=>'required|integer',
            
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $query=Subject_sub::leftjoin('subject','subject.id','=','subject_sub.subject_id')
                            ->leftjoin('base_table','base_table.id','=','subject.base_table_id')
                            ->leftjoin('board','board.id','=','base_table.board')
                            ->leftjoin('medium','medium.id','=','base_table.medium')
                            ->leftjoin('standard','standard.id','=','base_table.standard')
                            ->leftjoin('stream','stream.id','=','base_table.stream')
                            ->where('subject_sub.institute_id',$request->institute_id)
                            ->select('subject.id','subject_sub.amount','subject.name as subject_name',
                            'board.id as board_id','board.name as board_name','medium.id as medium_id',
                            'medium.name as medium_name','standard.id as standard_id',
                            'standard.name as standard_name','stream.id as stream_id','stream.name as stream_name');
                          
                            if (!empty($request->board_id)) {
                                $query->where('base_table.board', $request->board_id);
                            }
                            
                            if (!empty($request->standard_id)) {
                                $query->where('base_table.standard', $request->standard_id);
                            }
                            
                            if (!empty($request->medium_id)) {
                                $query->where('base_table.medium', $request->medium_id);
                            }
                            
                            if (!empty($request->stream_id)) {
                                $query->where('base_table.stream', $request->stream_id);
                            }
                            if (!empty($request->subject_id)) {
                                $query->where('subject_sub.subject_id', $request->subject_id);
                            }
                            
                            $subject = $query->get()->toarray();

        //    print_r($subject);exit;
                            
        $student = [];
        foreach($subject as $value){
                      $student[]=  ['subject_id'=>$value['id'],
                                    'subject_name'=>$value['subject_name'],
                                    'board_id'=>$value['board_id'],
                                    'board_name'=>$value['board_name'],
                                    'standard_id'=>$value['standard_id'],
                                    'standard_name'=>$value['standard_name'],
                                    'medium_id'=>$value['medium_id'],
                                    'medium_name'=>$value['medium_name'],
                                    'stream_id'=>$value['stream_id'],
                                    'stream_name'=>$value['stream_name'],
                                    'amount'=>(!empty($value['amount']))? $value['amount'] : '00.00',
                                ];
                        
              }
        return $this->response($student, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }


    }
    function subject_amount(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id'=>'required|integer',
            'subject_id'=>'required|integer',
            'amount'=>'required|integer',

            
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $subject_sub = Subject_sub::where('institute_id', $request->institute_id)->where('subject_id', $request->subject_id);
            $subject_sub->update([
            'amount'=>$request->amount
            ]);
            return $this->response([], "Fees Update Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    
}
