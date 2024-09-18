<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Discount_model;
use App\Models\Fees_colletion_model;
use App\Models\Student_detail;
use App\Models\Student_fees_model;
use App\Models\Subject_model;
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
    function fees_report_pdf2(Request $request){
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
                'users.mobile',
               
                'students_details.student_id',
                'batches.batch_name as batch_name',
                'board.name as board_name',
                'medium.name as medium_name',
                'standard.name as standard_name',
                'students_details.student_id'
            )
            ->where('students_details.status', '1')
            ->whereNull('users.deleted_at')
            ->whereNull('students_details.deleted_at')
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
            ->when(!empty($request->standard_id), function ($query) use ($request) { 
                return $query->where('students_details.standard_id', $request->standard_id);
            })
            ->when(!empty($request->institute_id), function ($query) use ($request) { 
                return $query->where('students_details.institute_id', $request->institute_id);
            })
            ->when(!empty($request->mobile), function ($query) use ($request) { 
                return $query->where('users.mobile', $request->mobile);
            })
            ->when(!empty($request->student_id), function ($query) use ($request) { 
                return $query->where('students_details.student_id', $request->student_id);
            });
        
        
      
        
        $student_response = $query->get()->toArray();
        // print_r($student_response);exit;
        $students = [];
        
        foreach ($student_response as $value) {
        
            // Fetch the total payment amount
            $fees_detail = Fees_colletion_model::where('student_id', $value['student_id'])
                ->where('institute_id', $request->institute_id)
                ->where('date', date('Y-m-d', strtotime($request->date)))
                ->select(DB::raw('SUM(payment_amount) as total_payment_amount'))
                ->first();
            $total_payment_amount = $fees_detail ? $fees_detail->total_payment_amount : 0;
           
            // Fetch the total fees and discount
            $student_fees = Student_fees_model::where('student_id', $value['id'])
                ->where('institute_id', $request->institute_id)
                ->first();
            $discount = Discount_model::where('institute_id', $request->institute_id)
                ->where('student_id', $value['id'])
                ->first();
               
            $total_fees = !empty($student_fees) ? $student_fees->total_fees : 0;
            $due_amount = $total_fees;
            if ($discount) {
                if ($discount->discount_by == 'Rupee') {
                    $due_amounts = $total_fees - $discount->discount_amount;
                } elseif ($discount->discount_by == 'Percentage') {
                    $due_amounts = $total_fees - ($total_fees * ($discount->discount_amount / 100));
                }
            }
        
            // Calculate due amount
            // print_r($due_amount);
            if(!empty($due_amount)){
                $due_amounts = $due_amount - $total_payment_amount;
            }
            
            // Determine status and append to students array
            if ($request->status == 'paid' && $due_amounts <= 0) {
                // Paid status
                $students[] = [
                    'student_id' => $value['id'],
                    'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                    'status' => 'paid',
                    'discount' => (!empty($discount->discount_amonut)) ? $discount->discount_amonut : '',
                    'total_fees' => !empty($total_fees) ? $total_fees : '',
                    'paid_amount' => !empty($total_payment_amount) ? $total_payment_amount : '',
                    'discount_by' => !empty($discount->discount_by) ? $discount->discount_by : '',
                    'board_name' =>$value['board_name'],
                    'batch_name' =>$value['batch_name'],
                    'medium_name' =>$value['medium_name'],
                    'standard_name' =>$value['standard_name'],
                    'mobile' =>$value['mobile'],

                ];
            } elseif ($request->status == 'pending' && $due_amounts > 0) {
                // Pending status
                $students[] = [
                    'student_id' => $value['id'],
                    'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                    'status' => 'pending',
                    'due_amount' => $due_amounts,
                    'total_fees' => !empty($total_fees) ? $total_fees : '',
                    'paid_amount' => !empty($total_payment_amount) ? $total_payment_amount : '',
                    'discount' => (!empty($discount->discount_amonut)) ? $discount->discount_amonut : '',
                    'discount_by' => !empty($discount->discount_by) ? $discount->discount_by : '',
                    'board_name' =>$value['board_name'],
                    'batch_name' =>$value['batch_name'],
                    'medium_name' =>$value['medium_name'],
                    'standard_name' =>$value['standard_name'],
                    'mobile' =>$value['mobile'],

                ];
            
            }
         elseif ($request->status == '') {
            // Pending status
            $students[] = [
                'student_id' => $value['id'],
                'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                'status' => (!empty($due_amount))?'pending':'paid',
                'due_amount' => (!empty($due_amounts))?$due_amounts:0,
                'total_fees' => !empty($total_fees) ? $total_fees : '',
                'paid_amount' => !empty($total_payment_amount) ? $total_payment_amount : 0,
                'discount' => (!empty($discount->discount_amonut)) ? $discount->discount_amonut : '',
                'discount_by' => !empty($discount->discount_by) ? $discount->discount_by : '',
                'board_name' =>$value['board_name'],
                'batch_name' =>$value['batch_name'],
                'medium_name' =>$value['medium_name'],
                'standard_name' =>$value['standard_name'],
                'mobile' =>$value['mobile'],

            ];
        
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
    function fees_report_pdf(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $board_id = !empty($request->board_id) ? $request->board_id :'';
            $board_response = Student_detail::leftjoin('board', 'board.id', '=', 'students_details.board_id')
                ->when(!empty($request->institute_id), function ($query) use ($request) {
                        return $query->where('students_details.institute_id', $request->institute_id);
                    })
                ->when(!empty($board_id), function ($query) use ($board_id) {
                            return $query->where('students_details.board_id', $board_id);
                        })
                ->distinct()
                ->select('board.id as board_id', 'board.name as board_name')
                ->get()->toarray();
            $board_result = [];
            foreach ($board_response as $board_value) {
                 $board_id = !empty($request->board_id) ? $request->board_id : $board_value['board_id'];
                 $medium_id = !empty($request->medium_id) ? $request->medium_id : '';
                 $medium_response = Student_detail::leftjoin('medium', 'medium.id', '=', 'students_details.medium_id')
                  ->when(!empty($request->institute_id), function ($query) use ($request) {
                    return $query->where('students_details.institute_id', $request->institute_id);
                  })
                  ->when(!empty($board_id), function ($query) use ($board_id) {
                    return $query->where('students_details.board_id', $board_id);
                    })
                  ->when(!empty($medium_id), function ($query) use ($medium_id) {
                    return $query->where('students_details.medium_id', $medium_id);
                  })
                    ->distinct()
                    ->select('medium.id as medium_id', 'medium.name as medium_name')
                    ->get()->toarray();
                $medium_result = [];
                foreach ($medium_response as $medium_value) {
                    $medium_id = !empty($request->medium_id) ? $request->medium_id : $medium_value['medium_id'];
                    $class_id = !empty($request->class_id) ? $request->class_id : '';
                    $class_response = Student_detail::leftjoin('class', 'class.id', '=', 'students_details.class_id')
                     ->when(!empty($request->institute_id), function ($query) use ($request) {
                        return $query->where('students_details.institute_id', $request->institute_id);
                      })
                      ->when(!empty($board_id), function ($query) use ($board_id) {
                        return $query->where('students_details.board_id', $board_id);
                        })
                      ->when(!empty($medium_id), function ($query) use ($medium_id) {
                        return $query->where('students_details.medium_id', $medium_id);
                      })
                      ->when(!empty($class_id), function ($query) use ($class_id) {
                        return $query->where('students_details.class_id', $class_id);
                      })
                    ->distinct()
                    ->select('class.id as class_id', 'class.name as class_name')
                    ->get()->toarray();
                    $class_result= [];
                    foreach($class_response as $class_value){
                        $class_id = !empty($request->class_id) ? $request->class_id : $class_value['class_id'];
                        $standard_id = !empty($request->standard_id) ? $request->standard_id : '';
                        $standard_response = Student_detail::leftjoin('standard', 'standard.id', '=', 'students_details.standard_id')
                          ->when(!empty($request->institute_id), function ($query) use ($request) {
                            return $query->where('students_details.institute_id', $request->institute_id);
                          })
                          ->when(!empty($board_id), function ($query) use ($board_id) {
                            return $query->where('students_details.board_id', $board_id);
                            })
                          ->when(!empty($medium_id), function ($query) use ($medium_id) {
                            return $query->where('students_details.medium_id', $medium_id);
                          })
                          ->when(!empty($class_id), function ($query) use ($class_id) {
                            return $query->where('students_details.class_id', $class_id);
                          })
                          ->when(!empty($standard_id), function ($query) use ($standard_id) {
                            return $query->where('students_details.standard_id', $standard_id);
                          }) 
                            ->distinct()
                            ->select('standard.id as standard_id', 'standard.name as standard_name')
                            ->get()->toarray();
                            
                           $standard_result=[];
                           foreach($standard_response as $standard_value){

                            $standard_id = !empty($request->standard_id) ? $request->standard_id : $standard_value['standard_id'];
                            $batch_id = !empty($request->batch_id) ? $request->batch_id : '';
                            $batch_response = Student_detail::leftjoin('batches', 'batches.id', '=', 'students_details.batch_id')
                              ->where('students_details.institute_id', $request->institute_id)
                              ->when(!empty($board_id), function ($query) use ($board_id) {
                                return $query->where('students_details.board_id', $board_id);
                                })
                              ->when(!empty($medium_id), function ($query) use ($medium_id) {
                                return $query->where('students_details.medium_id', $medium_id);
                              })
                              ->when(!empty($class_id), function ($query) use ($class_id) {
                                return $query->where('students_details.class_id', $class_id);
                              })
                              ->when(!empty($standard_id), function ($query) use ($standard_id) {
                                return $query->where('students_details.standard_id', $standard_id);
                              }) 
                              ->when(!empty($batch_id), function ($query) use ($batch_id) {
                                return $query->where('students_details.batch_id', $batch_id);
                              }) 
                            ->select('batches.id as batch_id', 'batches.batch_name as batch_name')
                            ->distinct()
                            ->get()->toarray(); 
                            // print_r($batch_response);exit;
                            $batch_result=[];
                             foreach($batch_response as $batch_value){
                                $query = Student_detail::leftJoin('users', 'users.id', '=', 'students_details.student_id')
                                ->select(
                                    'users.id',
                                    'users.firstname',
                                    'users.lastname',
                                    'users.image',
                                    'users.mobile',
                                   
                                    'students_details.student_id',
                                    'students_details.student_id'
                                )
                                ->where('students_details.status', '1')
                                ->whereNull('users.deleted_at')
                                ->whereNull('students_details.deleted_at')
                                ->groupBy(
                                    'users.id',
                                    'users.firstname',
                                    'users.lastname',
                                    'users.image',
                                   
                                    'students_details.student_id',
                                 )
                            
                                ->when(!empty($request->batch_id), function ($query) use ($request) {
                                    return $query->where('students_details.batch_id', $request->batch_id);
                                })
                                ->when(!empty($request->standard_id), function ($query) use ($request) { 
                                    return $query->where('students_details.standard_id', $request->standard_id);
                                })
                                ->when(!empty($request->institute_id), function ($query) use ($request) { 
                                    return $query->where('students_details.institute_id', $request->institute_id);
                                })
                                ->when(!empty($request->mobile), function ($query) use ($request) { 
                                    return $query->where('users.mobile', $request->mobile);
                                })
                                ->when(!empty($request->student_id), function ($query) use ($request) { 
                                    return $query->where('students_details.student_id', $request->student_id);
                                });
                                $student_response = $query->get()->toArray();
                                $students = [];
        
                                foreach ($student_response as $value) {
                                
                                    // Fetch the total payment amount
                                    $fees_detail = Fees_colletion_model::where('student_id', $value['student_id'])
                                        ->where('institute_id', $request->institute_id)
                                        ->where('date', date('Y-m-d', strtotime($request->date)))
                                        ->select(DB::raw('SUM(payment_amount) as total_payment_amount'))
                                        ->first();
                                    $total_payment_amount = $fees_detail ? $fees_detail->total_payment_amount : 0;
                                   
                                    // Fetch the total fees and discount
                                    $student_fees = Student_fees_model::where('student_id', $value['student_id'])
                                        ->where('institute_id', $request->institute_id)
                                        ->first();
                                        // print_r($student_fees);exit;
                                    $discount = Discount_model::where('institute_id', $request->institute_id)
                                        ->where('student_id', $value['id'])
                                        ->first();
                                       
                                    $total_fees = !empty($student_fees) ? $student_fees->total_fees : 0;
                                    $due_amount = $total_fees;
                                    if ($discount) {
                                        if ($discount->discount_by == 'Rupee') {
                                            $due_amounts = $total_fees - $discount->discount_amount;
                                        } elseif ($discount->discount_by == 'Percentage') {
                                            $due_amounts = $total_fees - ($total_fees * ($discount->discount_amount / 100));
                                        }
                                    }
                                
                                    // Calculate due amount
                                    // print_r($due_amount);
                                    if(!empty($due_amount)){
                                        $due_amounts = $due_amount - $total_payment_amount;
                                    }
                                    
                                    // Determine status and append to students array
                                    if ($request->status == 'paid' && $due_amounts <= 0) {
                                        // Paid status
                                        $students[] = [
                                            'student_id' => $value['id'],
                                            'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                                            'status' => 'paid',
                                            'discount' => (!empty($discount->discount_amonut)) ? $discount->discount_amonut : '',
                                            'total_fees' => !empty($total_fees) ? $total_fees : '',
                                            'paid_amount' => !empty($total_payment_amount) ? $total_payment_amount : '',
                                            'discount_by' => !empty($discount->discount_by) ? $discount->discount_by : '',
                                            'mobile' =>$value['mobile'],
                        
                                        ];
                                    } elseif ($request->status == 'pending' && $due_amounts > 0) {
                                        // Pending status
                                        $students[] = [
                                            'student_id' => $value['id'],
                                            'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                                            'status' => 'pending',
                                            'due_amount' => $due_amounts,
                                            'total_fees' => !empty($total_fees) ? $total_fees : '',
                                            'paid_amount' => !empty($total_payment_amount) ? $total_payment_amount : '',
                                            'discount' => (!empty($discount->discount_amonut)) ? $discount->discount_amonut : '',
                                            'discount_by' => !empty($discount->discount_by) ? $discount->discount_by : '',
                                            'mobile' =>$value['mobile'],
                        
                                        ];
                                    
                                    }
                                 elseif ($request->status == '') {
                                    // Pending status
                                    $students[] = [
                                        'student_id' => $value['id'],
                                        'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                                        'status' => (!empty($due_amount))?'pending':'paid',
                                        'due_amount' => (!empty($due_amounts))?$due_amounts:0,
                                        'total_fees' => !empty($total_fees) ? $total_fees : '',
                                        'paid_amount' => !empty($total_payment_amount) ? $total_payment_amount : 0,
                                        'discount' => (!empty($discount->discount_amonut)) ? $discount->discount_amonut : '',
                                        'discount_by' => !empty($discount->discount_by) ? $discount->discount_by : '',
                                        'mobile' =>$value['mobile'],
                        
                                    ];
                                
                                }
                                    
                                }
                                $batch_result[] = [
                                    'batch_id' => $batch_value['batch_id'],
                                    'batch_name' => $batch_value['batch_name'],
                                    'students' => $students,
                                    
                                ];
                             }
                            
                            $standard_result[] = [
                                    'standard_id' => $standard_value['standard_id'],
                                    'standard_name' => $standard_value['standard_name'],
                                    'batch'=>$batch_result
                                ];
                           } 
                        $class_result[] = [
                            'class_id' => $class_value['class_id'],
                            'class_name' => $class_value['class_name'],
                            'standard'=>$standard_result
                            
                        ];
                    }

                    $medium_result[] = [
                        'medium_id' => $medium_value['medium_id'],
                        'medium_name' => $medium_value['medium_name'],
                        'class' =>$class_result,
                        
                    ];

                }
                $board_result[] = [
                    'board_id' => $board_value['board_id'],
                    'board_name' => $board_value['board_name'],
                    'medium' => $medium_result,
                ];
            }
            print_r($board_result);exit;
            $pdf = PDF::loadView('pdf.paidfees', ['data' => $board_result]);
          
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
                return $this->response([], "Something want wrong!.", false, 400);
            }

    }
   
}
