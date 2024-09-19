<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Student_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Traits\ApiTrait;
use PDF;
use Illuminate\Support\Facades\Validator;
class StudentProgressPdfController extends Controller
{
    use ApiTrait;
    function studentprogress_report(Request $request){
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
                            $batch_result=[];
                              foreach($batch_response as $batch_value){
                                $batch_id = !empty($request->batch_id) ? $request->batch_id : $batch_value['batch_id'];
                          
                             
                                    
                                
                                $batch_result[] = [
                                    'batch_id' => $batch_value['batch_id'],
                                    'batch_name' => $batch_value['batch_name'],
                                 
                                    
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
            $data = ['board_result'=>$board_result,'request_data'=>$request];
            $pdf = PDF::loadView('pdf.studentprogressreport', ['data' => $data]);
          
            $folderPath = public_path('pdfs');

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $baseFileName = 'studentprogressreport.pdf';
            $pdfPath = $folderPath . '/' . $baseFileName;

            $counter = 1;
            while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/studentprogressreport' . $counter . '.pdf'; 
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
