<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Student_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use PDF;
use Illuminate\Support\Facades\Validator;

use ConsoleTVs\Charts\Facades\Charts;
use Chartisan\PHP\Chartisan;



class StudentProgressPdfController extends Controller
{
  use ApiTrait;
  function studentprogress_report(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'institute_id' => 'required',
    ]);
    if ($validator->fails()) {
      return $this->response([], $validator->errors()->first(), false, 400);
    }
    try {
      $board_id = !empty($request->board_id) ? $request->board_id : '';
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
          $class_result = [];
          foreach ($class_response as $class_value) {
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

            $standard_result = [];
            foreach ($standard_response as $standard_value) {

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
              $batch_result = [];
              foreach ($batch_response as $batch_value) {
                $batch_id = !empty($request->batch_id) ? $request->batch_id : $batch_value['batch_id'];

                $subject_get = Student_detail::leftjoin('standard', 'standard.id', '=', 'students_details.standard_id')
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
                  ->when(!empty($batch_id), function ($query) use ($batch_id) {
                    return $query->where('students_details.batch_id', $batch_id);
                  })

                  ->select('students_details.subject_id')
                  ->distinct()
                  ->pluck('students_details.subject_id');

                $mergedArray = [];
                foreach ($subject_get as $item) {
                  $mergedArray = array_merge($mergedArray, explode(',', $item));
                }

                $uniqueArray = array_unique($mergedArray);

                $uniqueArray = array_values($uniqueArray);

                $subject_id = !empty($request->subject_id) ? explode(',', $request->subject_id) : $uniqueArray;

                //         $subject_response = Subject_model::whereIn('id', $subject_all_get)
                //         ->select('subject.id as subject_id', 'subject.name as subject_name')
                //         ->get()->toarray();  
                //         $subject_result= [];

                //           foreach($subject_response as  $subject_value){
                // $subject_id=!empty($request->subject_id) ? $request->subject_id : $subject_value['subject_id'];

                // $subject_id_array = explode(',',$subject_ids);
                // print_r($subject_id);


              

                $exam_wise_student_response = Student_detail::leftJoin('users', 'users.id', '=', 'students_details.student_id')
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
                  ->when(!empty($batch_id), function ($query) use ($batch_id) {
                    return $query->where('students_details.batch_id', $batch_id);
                  })

                  ->where('students_details.reject_count', '0')
                  ->whereNull('students_details.deleted_at')
                  ->select('users.*')
                  ->distinct()
                  ->get()
                  ->toArray();


                $exam_student_result = [];
                foreach ($exam_wise_student_response as $exam_student_value) {

                  $exam_response = Student_detail::leftJoin('marks', 'marks.student_id', '=', 'students_details.student_id')
                    ->leftJoin('exam', 'exam.id', '=', 'marks.exam_id')
                    ->leftJoin('subject', 'subject.id', '=', 'exam.subject_id')
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
                    ->when(!empty($batch_id), function ($query) use ($batch_id) {
                      return $query->where('students_details.batch_id', $batch_id);
                    })
                    ->when(!empty($subject_id), function ($query) use ($subject_id) {
                      return $query->whereIn('exam.subject_id', $subject_id);
                    })
                    ->when(!empty($exam_student_value['id']), function ($query) use ($exam_student_value) {
                      return $query->where('marks.student_id', $exam_student_value['id']); // Fixed from whereIn to where
                    })
                    ->when(!empty($request->exam_name), function ($query) use ($request) {
                      return $query->where('exam.exam_title', $request->exam_name);
                    })
                    ->when(!empty($request->start_date) && !empty($request->end_date), function ($query) use ($request) {
                      return $query->whereBetween('exam.exam_date', [$request->start_date, $request->end_date]);
                    })
                    ->where('students_details.reject_count', '0')
                    ->whereNull('students_details.deleted_at')
                    ->select(
                      'marks.mark as marks_obtained',
                      'exam.total_mark as total_marks',
                      'exam.exam_title',
                      'exam.exam_date'
                    )
                    ->get()
                    ->toArray();

                  $exam_result = [];
                  foreach ($exam_response as $exam_value) {



                    $percentage = ($exam_value['marks_obtained'] / $exam_value['total_marks']) * 100;
                    $exam_result[] = [
                      'exam_name'  => $exam_value['exam_title'],
                      'mark'       => $exam_value['marks_obtained'],
                      'total_mark' => $exam_value['total_marks'],
                      'percentage' => round($percentage, 2),
                      'exam_date'  => $exam_value['exam_date'],
                    ];

                 

                  }
                  
                
                      $exam_student_result[] = [
                        'student_id' => $exam_student_value['id'],
                        'student_name' => $exam_student_value['firstname'] . '' . $exam_student_value['lastname'],
                        'exam'=>$exam_result,
                      ];
                    }
                 
                $batch_result[] = [
                  'batch_id' => $batch_value['batch_id'],
                  'batch_name' => $batch_value['batch_name'],
                  'student' => $exam_student_result,


                ];
              }

              $standard_result[] = [
                'standard_id' => $standard_value['standard_id'],
                'standard_name' => $standard_value['standard_name'],
                'batch' => $batch_result
              ];
            }
            $class_result[] = [
              'class_id' => $class_value['class_id'],
              'class_name' => $class_value['class_name'],
              'standard' => $standard_result

            ];
          }

          $medium_result[] = [
            'medium_id' => $medium_value['medium_id'],
            'medium_name' => $medium_value['medium_name'],
            'class' => $class_result,

          ];
        }
        $board_result[] = [
          'board_id' => $board_value['board_id'],
          'board_name' => $board_value['board_name'],
          'medium' => $medium_result,
        ];
      }
      // print_r($board_result);
      // exit;
      $data = ['board_result' => $board_result, 'request_data' => $request];
      return view('pdf.studentprogressreport',compact('data'));
      // $pdf = PDF::loadView('pdf.studentprogressreport', ['data' => $data]);
      
      // $pdf->setOption('enable-javascript',true);
      // $pdf->setOption('javascript-delay',1000);
      // $pdf->setOption('no-stop-slow-scripts',true);
      // $pdf->setOption('enable-smart-shrinking',true);

      // return $pdf->stream();

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
