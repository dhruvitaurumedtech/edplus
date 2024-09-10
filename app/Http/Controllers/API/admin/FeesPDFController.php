<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Discount_model;
use App\Models\Fees_colletion_model;
use App\Models\Student_detail;
use App\Models\Student_fees_model;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FeesPDFController extends Controller
{
    use ApiTrait;
    function fees_report_pdf(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $query = Student_detail::join('board', 'board.id', '=', 'students_details.board_id')
            ->leftJoin('medium', 'medium.id', '=', 'students_details.medium_id')
            ->leftJoin('batches', 'batches.id', '=', 'students_details.batch_id')
            ->leftJoin('standard', 'standard.id', '=', 'students_details.standard_id')
            ->leftJoin('users', 'users.id', '=', 'students_details.student_id')
            ->select(
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.image',
                'batches.batch_name as batch_name',
                'board.name as board_name',
                'medium.name as medium_name',
                'standard.name as standard_name',
                'students_details.student_id'
            )
            ->where('students_details.status', '1')
            ->groupBy(
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.image',
                'students_details.student_id',
                'batches.batch_name',
                'board.name',
                'medium.name',
                'standard.name'
            )
            ->when(!empty($request->batch_id), function ($query) use ($request) {
                return $query->where('students_details.batch_id', $request->batch_id);
            })
            ->when(!empty($request->standard_id), function ($query) use ($request) { // Corrected this part
                return $query->where('students_details.standard_id', $request->standard_id);
            });
        
        $student_response = $query->get()->toArray();
        
        $students = [];

        foreach ($student_response as $value) {
            $fees_detail = Fees_colletion_model::where('student_id', $value['student_id'])->where('institute_id', $request->institute_id)
                                                ->select(DB::raw('SUM(payment_amount) as total_payment_amount'))
                                                ->first();
            $total_payment_amount = $fees_detail->total_payment_amount;
            $student_fees = Student_fees_model::where('institute_id', $request->institute_id)->where('student_id', $value['student_id'])->first();


            $discount = Discount_model::where('institute_id', $request->institute_id)->where('student_id', $value['student_id'])->first();
            
            $total = null;
            if (!empty($student_fees) || !empty($discount)) {
                $total = (!empty($student_fees->total_fees))?$student_fees->total_fees:0;
                if ($discount) {
                    if ($discount->discount_by == 'Rupee') {
                        $total = $student_fees->total_fees - $discount->discount_amount;
                    } elseif ($discount->discount_by == 'Percentage') {
                        $total = $student_fees->total_fees - ($student_fees->total_fees * ($discount->discount_amount / 100));
                    }
                }
                
                if (!empty($total) && !empty($total_payment_amount)) {
                    if ($total == $total_payment_amount) {
                        
                        $students[] = [
                            'student_id' => $value['student_id'],
                            'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                            'status' => 'paid',
                            'discount' => (!empty($discount->discount_amonut)) ? $discount->discount_amonut : '',
                            'paid_amount' => !empty($total) ? $total : '',
                            'discount_by' => !empty($discount->discount_by) ? $discount->discount_by : '',
                            'board_name' =>$value['board_name'],
                            'batch_name' =>$value['batch_name'],
                            'medium_name' =>$value['medium_name'],
                            'standard_name' =>$value['standard_name']
                        ];
                       
                    }
                }
               
            }
        }
        $data = ['students'=>$students,'request_data'=>$request];
                
        
        $pdf = PDF::loadView('pdf.paidfees', ['data' => $data])->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);;

        $folderPath = public_path('pdfs');

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $baseFileName = 'paidfees.pdf';
        $pdfPath = $folderPath . '/' . $baseFileName;

        $counter = 1;
        while (File::exists($pdfPath)) {
            $pdfPath = $folderPath . '/paidfees' . $counter . '.pdf'; 
            $counter++;
        }
        
        file_put_contents($pdfPath, $pdf->output());
        $pdfUrl = asset('pdfs/' . basename($pdfPath));
        return $this->response($pdfUrl);
       
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
}
